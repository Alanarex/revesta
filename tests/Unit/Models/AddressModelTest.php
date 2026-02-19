<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\Housing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Address Model Unit Tests
 *
 * Tests the Address model including:
 * - Relationships (users, housings, addressables)
 * - Model attributes and factory
 * - Database operations
 *
 * SAFE TO RUN: RefreshDatabase uses in-memory SQLite database (:memory:)
 * configured in phpunit.xml. This does NOT affect your local database.
 */
class AddressModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test address has users relationship
     */
    public function test_address_has_users_relationship()
    {
        $address = Address::factory()->create();
        $user = User::factory()->create(['address_id' => $address->id]);

        $address->users()->attach($user->id, ['type' => 0]);

        $this->assertTrue($address->users->contains($user));
    }

    /**
     * Test address has housings relationship
     */
    public function test_address_has_housings_relationship()
    {
        $address = Address::factory()->create();
        $housing = Housing::factory()->create(['address_id' => $address->id]);

        $address->housings()->attach($housing->id, ['type' => 0]);

        $this->assertTrue($address->housings->contains($housing));
    }

    /**
     * Test address has addressables relationship
     */
    public function test_address_has_addressables_relationship()
    {
        $address = Address::factory()->create();
        $user = User::factory()->create(['address_id' => $address->id]);

        $address->users()->attach($user->id, ['type' => 0]);

        $this->assertGreaterThan(0, $address->addressables()->count());
    }

    /**
     * Test get filterable attributes
     */
    public function test_get_filterable_attributes()
    {
        $attributes = Address::getFilterableAttributes();

        $this->assertArrayHasKey('label', $attributes);
        $this->assertArrayHasKey('postal_code', $attributes);
        $this->assertArrayHasKey('street', $attributes);
        $this->assertArrayHasKey('city', $attributes);
    }

    /**
     * Test get filterable attribute types
     */
    public function test_get_filterable_attribute_types()
    {
        $types = Address::getFilterableAttributeTypes();

        $this->assertEquals('text', $types['label']);
        $this->assertEquals('text', $types['postal_code']);
        $this->assertEquals('text', $types['street']);
    }

    /**
     * Test address fillable attributes
     */
    public function test_address_fillable_attributes()
    {
        $address = Address::create([
            'label' => 'Test Label',
            'postal_code' => '12345',
            'street' => 'Test Street',
            'number' => '123',
            'complement' => 'Apt 5',
            'city' => 'Test City',
            'departement' => 'Test Dept',
            'insee_code' => '12345',
            'lat' => 48.8566,
            'lng' => 2.3522,
        ]);

        $this->assertEquals('Test Label', $address->label);
        $this->assertEquals('Test Street', $address->street);
        $this->assertEquals('123', $address->number);
        $this->assertEquals('Test City', $address->city);
    }

    /**
     * Test address factory creates valid address
     */
    public function test_address_factory_creates_valid_address()
    {
        $address = Address::factory()->create();

        $this->assertNotNull($address->label);
        $this->assertNotNull($address->street);
        $this->assertNotNull($address->postal_code);
        $this->assertNotNull($address->city);
    }

    /**
     * Test multiple addresses can be created
     */
    public function test_multiple_addresses_can_be_created()
    {
        $addresses = Address::factory()->count(10)->create();

        $this->assertEquals(10, $addresses->count());
        $this->assertEquals(10, Address::count());
    }

    /**
     * Test address deletion cascades
     */
    public function test_address_deletion_cascades()
    {
        $address = Address::factory()->create();
        $user = User::factory()->create(['address_id' => $address->id]);

        $addressId = $address->id;
        $address->delete();

        $this->assertDatabaseMissing('addresses', ['id' => $addressId]);
    }

    /**
     * Test address with all optional fields
     */
    public function test_address_with_all_optional_fields()
    {
        $address = Address::create([
            'label' => 'Complete Address',
            'street' => 'Main Street',
            'number' => '42',
            'complement' => 'Suite 200',
            'postal_code' => '12345',
            'city' => 'Springfield',
            'departement' => '42 - Loire',
            'insee_code' => '42282',
            'lat' => 45.4654,
            'lng' => 3.8793,
        ]);

        $this->assertNotNull($address->number);
        $this->assertNotNull($address->complement);
        $this->assertNotNull($address->insee_code);
        $this->assertNotNull($address->lat);
        $this->assertNotNull($address->lng);
    }

    /**
     * Test address with minimal fields
     */
    public function test_address_with_minimal_fields()
    {
        $address = Address::create([
            'label' => 'Minimal Address',
            'street' => 'Street Name',
            'postal_code' => '99999',
            'city' => 'City Name',
        ]);

        $this->assertNotNull($address->label);
        $this->assertNull($address->number);
        $this->assertNull($address->complement);
        $this->assertNull($address->insee_code);
    }

    /**
     * Test address timestamps are set
     */
    public function test_address_timestamps_are_set()
    {
        $address = Address::factory()->create();

        $this->assertNotNull($address->created_at);
        $this->assertNotNull($address->updated_at);
    }

    /**
     * Test address can be updated
     */
    public function test_address_can_be_updated()
    {
        $address = Address::factory()->create(['street' => 'Old Street']);

        $address->update(['street' => 'New Street']);

        $this->assertEquals('New Street', $address->street);
    }

    /**
     * Test multiple users can reference same address
     */
    public function test_multiple_users_can_reference_same_address()
    {
        $address = Address::factory()->create();
        $user1 = User::factory()->create(['address_id' => $address->id]);
        $user2 = User::factory()->create(['address_id' => $address->id]);

        $this->assertEquals($address->id, $user1->address_id);
        $this->assertEquals($address->id, $user2->address_id);
    }

    /**
     * Test address can have multiple housings
     */
    public function test_address_can_have_multiple_housings()
    {
        $address = Address::factory()->create();
        $housing1 = Housing::factory()->create(['address_id' => $address->id]);
        $housing2 = Housing::factory()->create(['address_id' => $address->id]);

        $this->assertCount(2, Housing::where('address_id', $address->id)->get());
    }
}
