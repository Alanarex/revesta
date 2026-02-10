<?php

namespace App\Policies;

use App\Models\Newsletter;
use App\Models\User;

class NewsletterPolicy
{
    /**
     * Determine whether the user can view any subscribers.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view a subscriber.
     */
    public function view(User $user, Newsletter $subscriber): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update a subscriber.
     */
    public function update(User $user, Newsletter $subscriber): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a subscriber.
     */
    public function delete(User $user, Newsletter $subscriber): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage subscribers (admin area).
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
