<?php

namespace Tests\Feature\Controllers;

use App\Models\Address;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Address Controller Feature Tests
 * 
 * Tests the HTTP controller layer including:
 * - CRUD operations (create, read, update, delete)
 * - Form submission and validation
 * - Flash messages and redirects
 * - View rendering
 * 
 * SAFE TO RUN: RefreshDatabase uses in-memory SQLite database (:memory:)
 * configured in phpunit.xml. This does NOT affect your local database.
 */
class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createAdminUser();
    }

    /**
     * Test index page loads
     */
    public function test_index_page_loads()
    {
        $response = $this->actingAs($this->user)->get(route('admin.addresses.index'));

        $response->assertStatus(200);
    }

    /**
     * Test index returns list url in meta tag
     */
    public function test_index_has_list_url_meta_tag()
    {
        $response = $this->actingAs($this->user)->get(route('admin.addresses.index'));

        $response->assertSee('addresses-list-url', false);
    }

    /**
     * Test create page loads
     */
    public function test_create_page_loads()
    {
        $response = $this->actingAs($this->user)->get(route('admin.addresses.create'));

        $response->assertStatus(200);
        $response->assertSee('Créer une nouvelle adresse');
    }

    /**
     * Test store creates address with valid data
     */
    public function test_store_creates_address_with_valid_data()
    {
        $data = [
            'street' => 'Rue de Test',
            'number' => '123',
            'postal_code' => '75001',
            'city' => 'Paris',
        ];

        $response = $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $response->assertRedirect(route('admin.addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'street' => 'Rue de Test',
            'city' => 'Paris',
        ]);
    }

    /**
     * Test store generates label automatically
     */
    public function test_store_generates_label_automatically()
    {
        $data = [
            'street' => 'Rue Nouvelle',
            'number' => '456',
            'postal_code' => '69000',
            'city' => 'Lyon',
        ];

        $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $address = Address::where('street', 'Rue Nouvelle')->first();

        $this->assertNotNull($address);
        $this->assertStringContainsString('456', $address->label);
        $this->assertStringContainsString('Rue Nouvelle', $address->label);
        $this->assertStringContainsString('69000', $address->label);
        $this->assertStringContainsString('Lyon', $address->label);
    }

    /**
     * Test store fails with missing required fields
     */
    public function test_store_fails_with_missing_street()
    {
        $data = [
            'postal_code' => '75001',
            'city' => 'Paris',
        ];

        $response = $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $response->assertSessionHasErrors('street');
        $this->assertDatabaseMissing('addresses', ['city' => 'Paris']);
    }

    /**
     * Test store fails with missing postal code
     */
    public function test_store_fails_with_missing_postal_code()
    {
        $data = [
            'street' => 'Rue de Test',
            'city' => 'Paris',
        ];

        $response = $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $response->assertSessionHasErrors('postal_code');
    }

    /**
     * Test store fails with missing city
     */
    public function test_store_fails_with_missing_city()
    {
        $data = [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
        ];

        $response = $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $response->assertSessionHasErrors('city');
    }

    /**
     * Test store redirects on success
     */
    public function test_store_redirects_on_success()
    {
        $data = [
            'street' => 'Test Street',
            'postal_code' => '12345',
            'city' => 'Test City',
        ];

        $response = $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $response->assertRedirect(route('admin.addresses.index'));
    }

    /**
     * Test store shows success message
     */
    public function test_store_shows_success_message()
    {
        $data = [
            'street' => 'Test Street',
            'postal_code' => '12345',
            'city' => 'Test City',
        ];

        $response = $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $response->assertSessionHas('success');
    }

    /**
     * Test edit page loads
     */
    public function test_edit_page_loads()
    {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.addresses.edit', $address));

        $response->assertStatus(200);
        $response->assertSee($address->street);
    }

    /**
     * Test edit page shows address details sidebar
     */
    public function test_edit_page_shows_sidebar()
    {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.addresses.edit', $address));

        $response->assertSee('Modifier l\'adresse');
        $response->assertSee($address->id);
    }

    /**
     * Test update changes address data
     */
    public function test_update_changes_address_data()
    {
        $address = Address::factory()->create(['street' => 'Old Street']);

        $data = [
            'street' => 'New Street',
            'postal_code' => $address->postal_code,
            'city' => $address->city,
        ];

        $this->actingAs($this->user)->putCsrf(route('admin.addresses.update', $address), $data);

        $address->refresh();

        $this->assertEquals('New Street', $address->street);
    }

    /**
     * Test update regenerates label
     */
    public function test_update_regenerates_label()
    {
        $address = Address::factory()->create([
            'street' => 'Old Street',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $oldLabel = $address->label;

        $data = [
            'street' => 'New Street',
            'postal_code' => '75001',
            'city' => 'Paris',
        ];

        $this->actingAs($this->user)->putCsrf(route('admin.addresses.update', $address), $data);

        $address->refresh();

        $this->assertNotEquals($oldLabel, $address->label);
        $this->assertStringContainsString('New Street', $address->label);
    }

    /**
     * Test update ignores submitted label
     */
    public function test_update_ignores_submitted_label()
    {
        $address = Address::factory()->create(['street' => 'Original Street']);

        $data = [
            'street' => 'Original Street',
            'postal_code' => $address->postal_code,
            'city' => $address->city,
            'label' => 'Custom Label Should Be Ignored',
        ];

        $this->actingAs($this->user)->putCsrf(route('admin.addresses.update', $address), $data);

        $address->refresh();

        $this->assertNotEquals('Custom Label Should Be Ignored', $address->label);
    }

    /**
     * Test update redirects on success
     */
    public function test_update_redirects_on_success()
    {
        $address = Address::factory()->create();

        $data = [
            'street' => $address->street,
            'postal_code' => $address->postal_code,
            'city' => $address->city,
        ];

        $response = $this->actingAs($this->user)->putCsrf(route('admin.addresses.update', $address), $data);

        $response->assertRedirect(route('admin.addresses.index'));
    }

    /**
     * Test update shows success message
     */
    public function test_update_shows_success_message()
    {
        $address = Address::factory()->create();

        $data = [
            'street' => $address->street,
            'postal_code' => $address->postal_code,
            'city' => $address->city,
        ];

        $response = $this->actingAs($this->user)->putCsrf(route('admin.addresses.update', $address), $data);

        $response->assertSessionHas('success');
    }

    /**
     * Test update fails with invalid data
     */
    public function test_update_fails_with_missing_required_fields()
    {
        $address = Address::factory()->create();

        $data = [
            'street' => '',
            'postal_code' => $address->postal_code,
            'city' => $address->city,
        ];

        $response = $this->actingAs($this->user)->putCsrf(route('admin.addresses.update', $address), $data);

        $response->assertSessionHasErrors('street');
    }

    /**
     * Test delete removes address
     */
    public function test_delete_removes_address()
    {
        $address = Address::factory()->create();
        $addressId = $address->id;

        $this->actingAs($this->user)->deleteCsrf(route('admin.addresses.destroy', $address));

        $this->assertDatabaseMissing('addresses', ['id' => $addressId]);
    }

    /**
     * Test delete redirects on success
     */
    public function test_delete_redirects_on_success()
    {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->deleteCsrf(route('admin.addresses.destroy', $address));

        $response->assertRedirect();
    }

    /**
     * Test create page shows breadcrumbs
     */
    public function test_create_page_shows_breadcrumbs()
    {
        $response = $this->actingAs($this->user)->get(route('admin.addresses.create'));

        $response->assertSee('breadcrumb');
    }

    /**
     * Test edit page shows breadcrumbs
     */
    public function test_edit_page_shows_breadcrumbs()
    {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.addresses.edit', $address));

        $response->assertSee('breadcrumb');
    }

    /**
     * Test store with all fields
     */
    public function test_store_with_all_fields()
    {
        $data = [
            'street' => 'Complete Street',
            'number' => '999',
            'complement' => 'Suite 100',
            'postal_code' => '54321',
            'city' => 'Complete City',
            'departement' => '54 - Meurthe-et-Moselle',
            'insee_code' => '54123',
            'lat' => 48.6921,
            'lng' => 6.1844,
        ];

        $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $this->assertDatabaseHas('addresses', [
            'street' => 'Complete Street',
            'number' => '999',
            'complement' => 'Suite 100',
            'city' => 'Complete City',
        ]);
    }

    /**
     * Test store with special characters
     */
    public function test_store_with_special_characters()
    {
        $data = [
            'street' => "Rue d'Alésia",
            'postal_code' => '75014',
            'city' => 'Paris',
        ];

        $this->actingAs($this->user)->postCsrf(route('admin.addresses.store'), $data);

        $this->assertDatabaseHas('addresses', [

            'street' => "Rue d'Alésia",
        ]);
    }
}
