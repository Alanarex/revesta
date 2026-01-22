<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

/**
 * Address Request Validation Tests
 * 
 * Tests form request validation including:
 * - Required field validation (street, postal_code, city)
 * - Field length constraints (255, 50, 10, etc.)
 * - Coordinate range validation (lat: -90 to 90, lng: -180 to 180)
 * - Type validation (string, numeric)
 * - Optional field handling
 * 
 * SAFE TO RUN: RefreshDatabase uses in-memory SQLite database (:memory:)
 * configured in phpunit.xml. This does NOT affect your local database.
 */
class AddressRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdminUser();
    }

    // ==================== STORE REQUEST TESTS ====================

    /**
     * Test store request rules exist
     */
    public function test_store_request_has_all_validation_rules()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $rules = $request->rules();
        $this->assertArrayHasKey('street', $rules);
        $this->assertArrayHasKey('postal_code', $rules);
        $this->assertArrayHasKey('city', $rules);
        $this->assertArrayHasKey('number', $rules);
        $this->assertArrayHasKey('complement', $rules);
        $this->assertArrayHasKey('label', $rules);
        $this->assertArrayHasKey('departement', $rules);
        $this->assertArrayHasKey('insee_code', $rules);
        $this->assertArrayHasKey('lat', $rules);
        $this->assertArrayHasKey('lng', $rules);
    }

    /**
     * Test store request validates required fields
     */
    public function test_store_request_requires_street()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => '',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('street', $validator->errors()->toArray());
    }

    public function test_store_request_requires_postal_code()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('postal_code', $validator->errors()->toArray());
    }

    public function test_store_request_requires_city()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => '',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('city', $validator->errors()->toArray());
    }

    /**
     * Test store request validates max length constraints
     */
    public function test_store_request_validates_street_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => str_repeat('a', 256),
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('street', $validator->errors()->toArray());
    }

    public function test_store_request_validates_postal_code_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '12345678901',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('postal_code', $validator->errors()->toArray());
    }

    public function test_store_request_validates_city_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => str_repeat('a', 256),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('city', $validator->errors()->toArray());
    }

    public function test_store_request_validates_number_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'number' => str_repeat('a', 51),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('number', $validator->errors()->toArray());
    }

    public function test_store_request_validates_complement_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'complement' => str_repeat('a', 256),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('complement', $validator->errors()->toArray());
    }

    public function test_store_request_validates_label_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'label' => str_repeat('a', 256),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('label', $validator->errors()->toArray());
    }

    public function test_store_request_validates_departement_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'departement' => str_repeat('a', 51),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('departement', $validator->errors()->toArray());
    }

    public function test_store_request_validates_insee_code_max_length()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'insee_code' => '12345678901',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('insee_code', $validator->errors()->toArray());
    }

    /**
     * Test store request validates coordinate ranges
     */
    public function test_store_request_rejects_latitude_below_minus_90()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'lat' => -91.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lat', $validator->errors()->toArray());
    }

    public function test_store_request_rejects_latitude_above_90()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'lat' => 91.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lat', $validator->errors()->toArray());
    }

    public function test_store_request_accepts_valid_latitude()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'lat' => 48.8566,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Valid latitude should pass validation');
    }

    public function test_store_request_rejects_longitude_below_minus_180()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'lng' => -181.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lng', $validator->errors()->toArray());
    }

    public function test_store_request_rejects_longitude_above_180()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'lng' => 181.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lng', $validator->errors()->toArray());
    }

    public function test_store_request_accepts_valid_longitude()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'lng' => 2.3522,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Valid longitude should pass validation');
    }

    /**
     * Test store request allows empty optional fields
     */
    public function test_store_request_allows_empty_optional_fields()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue de Test',
            'postal_code' => '75001',
            'city' => 'Paris',
            'number' => '',
            'complement' => '',
            'label' => '',
            'departement' => '',
            'insee_code' => '',
            'lat' => '',
            'lng' => '',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Empty optional fields should pass validation');
    }

    /**
     * Test store request accepts special characters
     */
    public function test_store_request_accepts_special_characters_in_street()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => "Rue d'Alésia",
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Special characters in street should pass validation');
    }

    /**
     * Test validate full valid payload passes
     */
    public function test_store_request_with_valid_full_data()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'label' => 'Home Address',
            'street' => 'Rue de Test',
            'number' => '123',
            'complement' => 'Apt 5',
            'postal_code' => '75001',
            'city' => 'Paris',
            'departement' => '75 - Paris',
            'insee_code' => '75056',
            'lat' => 48.8566,
            'lng' => 2.3522,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Complete valid payload should pass validation');
    }

    /**
     * Test validate only required fields pass
     */
    public function test_store_request_with_only_required_fields()
    {
        $request = StoreAddressRequest::create('POST', '/addresses', [
            'street' => 'Rue Minimale',
            'postal_code' => '75003',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Request with only required fields should pass');
    }

    // ==================== UPDATE REQUEST TESTS ====================

    /**
     * Test update request rules exist
     */
    public function test_update_request_has_all_validation_rules()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Updated Street',
            'postal_code' => '75002',
            'city' => 'Paris',
        ]);

        $rules = $request->rules();
        $this->assertArrayHasKey('street', $rules);
        $this->assertArrayHasKey('postal_code', $rules);
        $this->assertArrayHasKey('city', $rules);
        $this->assertArrayHasKey('number', $rules);
        $this->assertArrayHasKey('complement', $rules);
        $this->assertArrayHasKey('label', $rules);
        $this->assertArrayHasKey('departement', $rules);
        $this->assertArrayHasKey('insee_code', $rules);
        $this->assertArrayHasKey('lat', $rules);
        $this->assertArrayHasKey('lng', $rules);
    }

    /**
     * Test update request validates required fields
     */
    public function test_update_request_requires_street()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => '',
            'postal_code' => '75002',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('street', $validator->errors()->toArray());
    }

    public function test_update_request_requires_postal_code()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('postal_code', $validator->errors()->toArray());
    }

    public function test_update_request_requires_city()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => '',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('city', $validator->errors()->toArray());
    }

    /**
     * Test update request validates max length constraints
     */
    public function test_update_request_validates_street_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => str_repeat('a', 256),
            'postal_code' => '75002',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('street', $validator->errors()->toArray());
    }

    public function test_update_request_validates_postal_code_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '12345678901',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('postal_code', $validator->errors()->toArray());
    }

    public function test_update_request_validates_city_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => str_repeat('a', 256),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('city', $validator->errors()->toArray());
    }

    public function test_update_request_validates_number_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'number' => str_repeat('a', 51),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('number', $validator->errors()->toArray());
    }

    public function test_update_request_validates_complement_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'complement' => str_repeat('a', 256),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('complement', $validator->errors()->toArray());
    }

    public function test_update_request_validates_label_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'label' => str_repeat('a', 256),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('label', $validator->errors()->toArray());
    }

    public function test_update_request_validates_departement_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'departement' => str_repeat('a', 51),
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('departement', $validator->errors()->toArray());
    }

    public function test_update_request_validates_insee_code_max_length()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'insee_code' => '12345678901',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('insee_code', $validator->errors()->toArray());
    }

    /**
     * Test update request validates coordinate ranges
     */
    public function test_update_request_rejects_latitude_below_minus_90()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'lat' => -91.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lat', $validator->errors()->toArray());
    }

    public function test_update_request_rejects_latitude_above_90()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'lat' => 91.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lat', $validator->errors()->toArray());
    }

    public function test_update_request_accepts_valid_latitude()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'lat' => 48.8566,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Valid latitude should pass validation');
    }

    public function test_update_request_rejects_longitude_below_minus_180()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'lng' => -181.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lng', $validator->errors()->toArray());
    }

    public function test_update_request_rejects_longitude_above_180()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'lng' => 181.0,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('lng', $validator->errors()->toArray());
    }

    public function test_update_request_accepts_valid_longitude()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'lng' => 2.3522,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Valid longitude should pass validation');
    }

    /**
     * Test update request allows empty optional fields
     */
    public function test_update_request_allows_empty_optional_fields()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'number' => '',
            'complement' => '',
            'label' => '',
            'departement' => '',
            'insee_code' => '',
            'lat' => '',
            'lng' => '',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Empty optional fields should pass validation');
    }

    /**
     * Test update request accepts special characters
     */
    public function test_update_request_accepts_special_characters_in_street()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => "Rue d'Alésia",
            'postal_code' => '75002',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Special characters in street should pass validation');
    }

    /**
     * Test update request label is sometimes (optional) rule
     */
    public function test_update_request_label_is_sometimes_nullable()
    {
        $address = Address::factory()->create();

        // Test with null label
        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
            'label' => null,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Null label should pass validation');

        // Test without label field
        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Missing label should pass validation');
    }

    /**
     * Test update request label is not required (unlike store)
     */
    public function test_update_request_label_is_sometimes_not_required()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue de Test',
            'postal_code' => '75002',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Label should not be required in update');
    }

    /**
     * Test validate full valid payload passes
     */
    public function test_update_request_with_valid_full_data()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'label' => 'Updated Address',
            'street' => 'Rue Mise à Jour',
            'number' => '456',
            'complement' => 'Suite 2',
            'postal_code' => '75002',
            'city' => 'Paris',
            'departement' => '75 - Paris',
            'insee_code' => '75056',
            'lat' => 48.8500,
            'lng' => 2.3600,
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Complete valid payload should pass validation');
    }

    /**
     * Test validate only required fields pass
     */
    public function test_update_request_with_only_required_fields()
    {
        $address = Address::factory()->create();

        $request = UpdateAddressRequest::create('PUT', "/addresses/{$address->id}", [
            'street' => 'Rue Minimale',
            'postal_code' => '75003',
            'city' => 'Paris',
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertFalse($validator->fails(), 'Request with only required fields should pass');
    }
}
