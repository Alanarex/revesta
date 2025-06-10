<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Simulation;
use App\Models\User;

class SimulationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            Simulation::factory()->count(2)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}