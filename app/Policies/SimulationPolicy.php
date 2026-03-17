<?php

namespace App\Policies;

use App\Models\Simulation;
use App\Models\User;

class SimulationPolicy
{
    public function submit(?User $user = null): bool
    {
        return true;
    }

    public function view(User $user, Simulation $simulation): bool
    {
        return $user->isAdmin() || $simulation->user_id === $user->id;
    }
}
