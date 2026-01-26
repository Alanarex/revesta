<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function update(User $user, array $attributes): User
    {
        $user->fill($attributes);
        $user->save();
        return $user;
    }

    public function findByEmail(?string $email): ?User
    {
        if (empty($email)) {
            return null;
        }

        return User::where('email', $email)->first();
    }
}
