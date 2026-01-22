<?php

namespace App\Services;

use App\Models\Address;
use App\Repositories\AddressRepository;

class AddressService
{
    public function __construct(
        protected AddressRepository $addressRepository
    ) {}

    /**
     * Get all addresses with pagination.
     */
    public function getAllAddresses(int $perPage = 20, ?string $search = null, ?string $sortColumn = null, ?string $sortDirection = 'asc')
    {
        return $this->addressRepository->getAll($perPage, $search, $sortColumn, $sortDirection);
    }

    /**
     * Find an address by ID with all relations.
     */
    public function findAddress(int $id): ?Address
    {
        return $this->addressRepository->findWithRelations($id);
    }

    /**
     * Create a new address.
     */
    public function createAddress(array $data): Address
    {
        // Always auto-generate label from address components
        // This prevents users from bypassing the frontend readonly by modifying the DOM
        $data['label'] = $this->generateLabel($data);

        return $this->addressRepository->create($data);
    }

    /**
     * Update an address.
     */
    public function updateAddress(Address $address, array $data): Address
    {
        // Always auto-generate label from address components
        // Merge current values with new data to generate label with updated fields
        $mergedData = array_merge([
            'street' => $address->street,
            'number' => $address->number,
            'postal_code' => $address->postal_code,
            'city' => $address->city,
        ], $data);
        $data['label'] = $this->generateLabel($mergedData);

        return $this->addressRepository->update($address, $data);
    }

    /**
     * Delete an address.
     */
    public function deleteAddress(Address $address): bool
    {
        return $this->addressRepository->delete($address);
    }

    /**
     * Generate address label from components.
     * Format: [number] street, postal_code city
     */
    private function generateLabel(array $data): string
    {
        $streetParts = [];
        $locationParts = [];

        if (isset($data['number']) && $data['number']) {
            $streetParts[] = $data['number'];
        }

        if (isset($data['street']) && $data['street']) {
            $streetParts[] = $data['street'];
        }

        if (isset($data['postal_code']) && $data['postal_code']) {
            $locationParts[] = $data['postal_code'];
        }

        if (isset($data['city']) && $data['city']) {
            $locationParts[] = $data['city'];
        }

        $street = implode(' ', $streetParts);
        $location = implode(' ', $locationParts);

        if ($street && $location) {
            return "{$street}, {$location}";
        }

        return $street ?: $location;
    }

    /**
     * Check if address already exists.
     */
    public function addressExists(string $street, ?string $number, string $postalCode, string $city, ?int $exceptId = null): bool
    {
        return $this->addressRepository->existsByFields($street, $number, $postalCode, $city, $exceptId);
    }

    /**
     * Get addresses by model type.
     */
    public function getAddressesByModelType(string $modelType, int $perPage = 20)
    {
        return $this->addressRepository->getByModelType($modelType, $perPage);
    }

    /**
     * Get address count.
     */
    public function getAddressCount(): int
    {
        return $this->addressRepository->count();
    }
}
