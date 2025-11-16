<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function __construct(
        private readonly UserRepository $users
    ) {}

    public function updateProfile(User $user, array $data): User
    {
        // If email changed, force re-verification
        if (array_key_exists('email', $data) && $data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        return $this->users->update($user, [
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name'  => $data['last_name'] ?? $user->last_name,
            'email'      => $data['email'] ?? $user->email,
            'phone'      => $data['phone'] ?? $user->phone,
            'civil_status' => $data['civil_status'] ?? $user->civil_status,
            'family_status' => $data['family_status'] ?? $user->family_status,
        ]);
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();
    }

    public function deleteAccount(User $user): bool
    {
        return $user->delete();
    }
}
