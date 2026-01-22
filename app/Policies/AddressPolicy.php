<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * Determine whether the user can view any addresses.
     * Only admins can view addresses.
     */
    public function viewAny(?User $user): bool
    {
        return $user !== null && $user->isAdmin();
    }

    /**
     * Determine whether the user can manage addresses.
     * Only admins can manage addresses.
     */
    public function manage(?User $user): bool
    {
        return $user !== null && $user->isAdmin();
    }

    /**
     * Determine whether the user can view the address.
     * Only admins can view addresses.
     */
    public function view(?User $user, Address $address): bool
    {
        return $user !== null && $user->isAdmin();
    }

    /**
     * Determine whether the user can create addresses.
     * Only admins can create addresses.
     */
    public function create(User $user): bool
    {
        return $user !== null && $user->isAdmin();
    }

    /**
     * Determine whether the user can update the address.
     * Only admins can update addresses.
     */
    public function update(User $user, Address $address): bool
    {
        return $user !== null && $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the address.
     * Only admins can delete addresses.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user !== null && $user->isAdmin();
    }
}
