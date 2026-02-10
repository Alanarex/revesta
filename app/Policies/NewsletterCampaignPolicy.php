<?php

namespace App\Policies;

use App\Models\NewsletterCampaign;
use App\Models\User;

class NewsletterCampaignPolicy
{
    /**
     * Determine whether the user can view any campaigns.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the campaign.
     */
    public function view(User $user, NewsletterCampaign $campaign): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create campaigns.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the campaign.
     */
    public function update(User $user, NewsletterCampaign $campaign): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the campaign.
     */
    public function delete(User $user, NewsletterCampaign $campaign): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage campaigns (admin area).
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
