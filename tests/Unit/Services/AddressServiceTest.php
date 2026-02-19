<?php

namespace Tests\Unit\Services;

use App\Models\Address;
use App\Repositories\AddressRepository;
use App\Services\AddressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AddressService Unit Tests
 *
 * Tests the address service business logic including:
 * - Label generation from address components
 * - Address creation and updates
 * - Label regeneration security (prevents manual label manipulation)
 *
 * SAFE TO RUN: RefreshDatabase uses in-memory SQLite database (:memory:)
 * configured in phpunit.xml. This does NOT affect your local database.
 */
class AddressServiceTest extends TestCase
{
    use RefreshDatabase;

    private AddressService $addressService;

    private AddressRepository $addressRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addressRepository = app(AddressRepository::class);
        $this->addressService = new AddressService($this->addressRepository);
    }

    /**
     * Test label generation with all fields
     */
    public function test_generate_label_with_all_fields()
    {
        $data = [
            'number' => '123',
            'street' => 'Rue de Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
        ];

        $address = $this->addressService->createAddress($data);

        $this->assertEquals('123 Rue de Paris, 75001 Paris', $address->label);
    }

    /**
     * Test label generation without number
     */
    public function test_generate_label_without_number()
    {
        $data = [
            'street' => 'Rue de Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
        ];

        $address = $this->addressService->createAddress($data);

        $this->assertEquals('Rue de Paris, 75001 Paris', $address->label);
    }

    /**
     * Test label generation with only required fields
     */
    public function test_generate_label_minimal()
    {
        $data = [
            'street' => 'Avenue Montaigne',
            'postal_code' => '75008',
            'city' => 'Paris',
        ];

        $address = $this->addressService->createAddress($data);

        $this->assertStringContainsString('Avenue Montaigne', $address->label);
        $this->assertStringContainsString('75008', $address->label);
        $this->assertStringContainsString('Paris', $address->label);
    }

    /**
     * Test create address with all optional fields
     */
    public function test_create_address_with_all_fields()
    {
        $data = [
            'number' => '88',
            'street' => 'Rue de la Paix',
            'complement' => 'Appartement 5',
            'postal_code' => '69000',
            'city' => 'Lyon',
            'departement' => '69 - Rhône',
            'insee_code' => '69123',
            'lat' => 45.7640,
            'lng' => 4.8357,
        ];

        $address = $this->addressService->createAddress($data);

        $this->assertDatabaseHas('addresses', [
            'street' => 'Rue de la Paix',
            'number' => '88',
            'complement' => 'Appartement 5',
            'city' => 'Lyon',
            'departement' => '69 - Rhône',
            'insee_code' => '69123',
        ]);
    }

    /**
     * Test update address regenerates label
     */
    public function test_update_address_regenerates_label()
    {
        $address = Address::factory()->create([
            'number' => '10',
            'street' => 'Old Street',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $oldLabel = $address->label;

        $this->addressService->updateAddress($address, [
            'number' => '20',
            'street' => 'New Street',
        ]);

        $address->refresh();

        $this->assertNotEquals($oldLabel, $address->label);
        $this->assertStringContainsString('20', $address->label);
        $this->assertStringContainsString('New Street', $address->label);
    }

    /**
     * Test update address ignores submitted label
     */
    public function test_update_address_ignores_submitted_label()
    {
        $address = Address::factory()->create([
            'street' => 'Original Street',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $submittedLabel = 'Custom Label That Should Be Ignored';

        $this->addressService->updateAddress($address, [
            'label' => $submittedLabel,
            'street' => 'Original Street',
        ]);

        $address->refresh();

        $this->assertNotEquals($submittedLabel, $address->label);
        $this->assertStringContainsString('Original Street', $address->label);
    }

    /**
     * Test find address with relations
     */
    public function test_find_address_with_relations()
    {
        $address = Address::factory()->create();

        $foundAddress = $this->addressService->findAddress($address->id);

        $this->assertNotNull($foundAddress);
        $this->assertEquals($address->id, $foundAddress->id);
    }

    /**
     * Test find non-existent address
     */
    public function test_find_non_existent_address()
    {
        $foundAddress = $this->addressService->findAddress(99999);

        $this->assertNull($foundAddress);
    }

    /**
     * Test delete address
     */
    public function test_delete_address()
    {
        $address = Address::factory()->create();
        $addressId = $address->id;

        $result = $this->addressService->deleteAddress($address);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('addresses', ['id' => $addressId]);
    }

    /**
     * Test get all addresses with pagination
     */
    public function test_get_all_addresses_with_pagination()
    {
        Address::factory()->count(25)->create();

        $addresses = $this->addressService->getAllAddresses(perPage: 10);

        $this->assertEquals(10, count($addresses->items()));
        $this->assertEquals(25, $addresses->total());
        $this->assertEquals(1, $addresses->currentPage());
    }

    /**
     * Test label with special characters
     */
    public function test_generate_label_with_special_characters()
    {
        $data = [
            'number' => '123bis',
            'street' => 'Rue d\'Alésia',
            'postal_code' => '75014',
            'city' => 'Paris',
        ];

        $address = $this->addressService->createAddress($data);

        $this->assertEquals('123bis Rue d\'Alésia, 75014 Paris', $address->label);
    }

    /**
     * Test label updates when only one field changes
     */
    public function test_update_only_street_regenerates_label()
    {
        $address = Address::factory()->create([
            'number' => '50',
            'street' => 'Rue A',
            'postal_code' => '75001',
            'city' => 'Paris',
        ]);

        $this->addressService->updateAddress($address, [
            'street' => 'Rue B',
        ]);

        $address->refresh();

        $this->assertStringContainsString('Rue B', $address->label);
        $this->assertStringContainsString('50', $address->label);
    }

    /**
     * Test address with null optional fields
     */
    public function test_create_address_with_null_optional_fields()
    {
        $data = [
            'street' => 'Main Street',
            'postal_code' => '12345',
            'city' => 'Somewhere',
            'number' => null,
            'complement' => null,
            'insee_code' => null,
            'lat' => null,
            'lng' => null,
        ];

        $address = $this->addressService->createAddress($data);

        $this->assertNotNull($address->label);
        $this->assertNull($address->number);
        $this->assertNull($address->insee_code);
    }
}
