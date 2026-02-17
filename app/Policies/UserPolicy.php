<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Alias for admin checks used by Gate::allows('manage', User::class)
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ?User $model = null): bool
    {
        if (is_null($model)) {
            return $user->isAdmin();
        }

        return $user->id === $model->id || $user->isAdmin();
    }

    public function update(User $user, ?User $model = null): bool
    {
        if (is_null($model)) {
            return $user->isAdmin();
        }

        return $user->id === $model->id || $user->isAdmin();
    }

    public function resetPassword(User $user, ?User $model = null): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    public function delete(User $user, ?User $model = null): bool
    {
        if (is_null($model)) {
            return $user->isAdmin();
        }

        return $user->id === $model->id || $user->isAdmin();
    }

    public function toggleActive(User $user, ?User $model = null): bool
    {
        if (is_null($model)) {
            return $user->isAdmin();
        }

        return $user->id === $model->id || $user->isAdmin();
    }
}
