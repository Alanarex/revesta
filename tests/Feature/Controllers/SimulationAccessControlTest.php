<?php

namespace Tests\Feature\Controllers;

use App\Models\Ad;
use App\Models\Role;
use App\Models\Simulation;
use App\Models\User;
use Tests\TestCase;

class SimulationAccessControlTest extends TestCase
{
    public function test_regular_user_index_shows_only_their_simulations(): void
    {
        $userRole = Role::factory()->create([
            'name' => 'user',
            'display_name' => 'Utilisateur',
        ]);

        $owner = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        $otherUser = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        $ownerAd = Ad::factory()->create([
            'titre' => 'Annonce propriétaire visible',
        ]);

        $otherAd = Ad::factory()->create([
            'titre' => 'Annonce autre utilisateur cachée',
        ]);

        Simulation::factory()->create([
            'user_id' => $owner->id,
            'ad_id' => $ownerAd->id,
        ]);

        Simulation::factory()->create([
            'user_id' => $otherUser->id,
            'ad_id' => $otherAd->id,
        ]);

        $response = $this->actingAs($owner)->get(route('simulations.index'));

        $response->assertOk();
        $response->assertSee('Annonce propriétaire visible');
        $response->assertDontSee('Annonce autre utilisateur cachée');
    }

    public function test_regular_user_cannot_view_another_users_simulation_show_page(): void
    {
        $userRole = Role::factory()->create([
            'name' => 'user',
            'display_name' => 'Utilisateur',
        ]);

        $owner = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        $otherUser = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        $ownerAd = Ad::factory()->create();
        $otherAd = Ad::factory()->create();

        $ownerSimulation = Simulation::factory()->create([
            'user_id' => $owner->id,
            'ad_id' => $ownerAd->id,
        ]);

        $otherSimulation = Simulation::factory()->create([
            'user_id' => $otherUser->id,
            'ad_id' => $otherAd->id,
        ]);

        $this->actingAs($owner)
            ->get(route('simulations.show', $ownerSimulation))
            ->assertOk();

        $this->actingAs($owner)
            ->get(route('simulations.show', $otherSimulation))
            ->assertForbidden();
    }
}
