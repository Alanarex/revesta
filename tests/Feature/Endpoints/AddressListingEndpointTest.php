<?php

namespace Tests\Feature\Endpoints;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Address Listing Endpoint Tests
 *
 * Tests the internal AJAX endpoint for listing addresses including:
 * - Pagination
 * - Search functionality
 * - Sorting
 * - JSON response structure
 * - Combined search + sort operations
 *
 * NOTE: This is an internal endpoint used by datatable.js, not a public REST API.
 * For external API endpoints, see tests/Feature/API/
 *
 * SAFE TO RUN: RefreshDatabase uses in-memory SQLite database (:memory:)
 * configured in phpunit.xml. This does NOT affect your local database.
 */
class AddressListingEndpointTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createAdminUser();
    }

    /**
     * Test get addresses list endpoint
     */
    public function test_get_addresses_list()
    {
        Address::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'total', 'per_page', 'current_page']);
    }

    /**
     * Test list pagination
     */
    public function test_list_pagination()
    {
        Address::factory()->count(25)->create();

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list', ['page' => 1, 'per_page' => 10]));

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }

    /**
     * Test list with search
     */
    public function test_list_with_search()
    {
        Address::factory()->create(['city' => 'Paris']);
        Address::factory()->create(['city' => 'Lyon']);

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list', ['search' => 'Paris']));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('total'));
    }

    /**
     * Test list search by id
     */
    public function test_list_search_by_id()
    {
        $address1 = Address::factory()->create();
        $address2 = Address::factory()->create();

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list', ['search' => $address1->label]));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('total'));
        $this->assertEquals($address1->label, $response->json('data.0.label'));
    }

    /**
     * Test list with sort
     */
    public function test_list_with_sort()
    {
        Address::factory()->create(['number' => '1', 'street' => 'A Street']);
        Address::factory()->create(['number' => '999', 'street' => 'Z Street']);

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list', ['sort' => 'street', 'direction' => 'asc']));

        $response->assertStatus(200);
        $streets = collect($response->json('data'))->pluck('street')->toArray();
        $this->assertEquals('1 A Street', $streets[0]);
    }

    /**
     * Test list sort descending
     */
    public function test_list_sort_descending()
    {
        Address::factory()->create(['number' => '1', 'street' => 'A Street']);
        Address::factory()->create(['number' => '999', 'street' => 'Z Street']);

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list', ['sort' => 'street', 'direction' => 'desc']));

        $response->assertStatus(200);
        $streets = collect($response->json('data'))->pluck('street')->toArray();
        $this->assertEquals('999 Z Street', $streets[0]);
    }

    /**
     * Test list returns correct fields
     */
    public function test_list_returns_correct_fields()
    {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list'));

        $response->assertStatus(200);
        $this->assertArrayHasKey('id', $response->json('data.0'));
        $this->assertArrayHasKey('label', $response->json('data.0'));
        $this->assertArrayHasKey('street', $response->json('data.0'));
        $this->assertArrayHasKey('postal_code', $response->json('data.0'));
        $this->assertArrayHasKey('city', $response->json('data.0'));
    }

    /**
     * Test list empty result
     */
    public function test_list_empty_result()
    {
        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list'));

        $response->assertStatus(200);

        // Admin user has one address from factory, so total should be 1
        $this->assertEquals(1, $response->json('total'));
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test list combined search and sort
     */
    public function test_list_combined_search_and_sort()
    {
        Address::factory()->create(['city' => 'Paris', 'street' => 'Rue A']);
        Address::factory()->create(['city' => 'Paris', 'street' => 'Rue Z']);

        $response = $this->actingAs($this->user)->getJson(
            route('admin.addresses.list', ['search' => 'Paris', 'sort' => 'street', 'direction' => 'desc'])
        );

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('total'));
    }

    /**
     * Test list all addresses returned
     */
    public function test_list_returns_all_addresses_data()
    {
        $addresses = Address::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list', ['per_page' => 50]));

        $response->assertStatus(200);
        $this->assertEquals(4, $response->json('total'));
    }

    /**
     * Test list preserves address properties
     */
    public function test_list_preserves_address_properties()
    {
        $address = Address::factory()->create([
            'number' => '42',
            'street' => 'Test Street',
            'postal_code' => '12345',
            'city' => 'Test City',
            'departement' => 'Test Dept',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list'));

        $response->assertStatus(200);
        $data = collect($response->json('data'))->firstWhere('id', $address->id);

        $this->assertEquals('42 Test Street', $data['street']);
        $this->assertEquals('12345', $data['postal_code']);
        $this->assertEquals('Test City', $data['city']);
    }

    /**
     * Test list default per_page
     */
    public function test_list_default_per_page()
    {
        Address::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson(route('admin.addresses.list'));

        $response->assertStatus(200);
        $this->assertLessThanOrEqual(50, $response->json('per_page'));
    }
}
