<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function registerAndLogin(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? null, // Add role if provided
        ]);

        event(new Registered($user));

        // Send verification email instead of auto-login
        app(\App\Services\VerificationService::class)->sendVerification($user);

        return $user;
    }
}
