<?php

namespace App\Repositories;

use App\Models\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressRepository
{
    /**
     * Get all addresses with optional filtering and pagination.
     */
    public function getAll(int $perPage = 20, ?string $search = null, ?string $sortColumn = null, ?string $sortDirection = 'asc'): LengthAwarePaginator
    {
        $query = Address::select([
            'id',
            'label',
            'street',
            'number',
            'postal_code',
            'city',
            'departement',
            'created_at',
            'updated_at',
        ]);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%')
                    ->orWhere('label', 'like', '%'.$search.'%')
                    ->orWhere('street', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('postal_code', 'like', '%'.$search.'%')
                    ->orWhere('departement', 'like', '%'.$search.'%');
            });
        }

        // Apply sorting
        $validColumns = ['id', 'label', 'street', 'postal_code', 'city', 'departement', 'created_at', 'updated_at'];
        $sortColumn = in_array($sortColumn, $validColumns) ? $sortColumn : 'label';
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Find an address by ID with relations.
     */
    public function findWithRelations(int $id): ?Address
    {
        return Address::with(['users', 'housings', 'addressables'])->find($id);
    }

    /**
     * Find an address by ID.
     */
    public function find(int $id): ?Address
    {
        return Address::find($id);
    }

    /**
     * Create a new address.
     */
    public function create(array $data): Address
    {
        return Address::create($data);
    }

    /**
     * Update an address.
     */
    public function update(Address $address, array $data): Address
    {
        $address->update($data);

        return $address->fresh();
    }

    /**
     * Delete an address.
     */
    public function delete(Address $address): bool
    {
        return $address->delete();
    }

    /**
     * Check if an address exists by unique fields.
     */
    public function existsByFields(string $street, ?string $number, string $postalCode, string $city, ?int $exceptId = null): bool
    {
        $query = Address::where('street', $street)
            ->where('postal_code', $postalCode)
            ->where('city', $city);

        if ($number !== null) {
            $query->where('number', $number);
        } else {
            $query->whereNull('number');
        }

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    /**
     * Get addresses used by a specific model type.
     */
    public function getByModelType(string $modelType, int $perPage = 20): LengthAwarePaginator
    {
        return Address::whereHas('addressables', function ($q) use ($modelType) {
            $q->where('addressable_type', $modelType);
        })
            ->with(['addressables' => function ($q) use ($modelType) {
                $q->where('addressable_type', $modelType);
            }])
            ->paginate($perPage);
    }

    /**
     * Get address count.
     */
    public function count(): int
    {
        return Address::count();
    }
}
