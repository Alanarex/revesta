<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected UserRepository $userRepository) {}

    /**
     * Authenticate using email/password and log the user in.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(array $credentials, bool $remember = false): void
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Check if email is verified
        if (! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => 'Votre email n\'a pas ete verifie. Veuillez verifier votre email et definir votre mot de passe.',
            ]);
        }

        Auth::guard('web')->login($user, $remember);
    }

    /**
     * Confirm a user's password, throwing a ValidationException on failure.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function confirmPassword(User $user, ?string $password): void
    {
        if (! $password || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => Lang::get('auth.password'),
            ]);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
