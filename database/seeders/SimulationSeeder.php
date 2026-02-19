<?php

namespace Database\Seeders;

use App\Models\Simulation;
use App\Models\User;
use Illuminate\Database\Seeder;

class SimulationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating simulations for users...');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found! Please run UserSeeder first.');

            return;
        }

        $totalSimulations = 0;
        foreach ($users as $user) {
            $count = 2;
            Simulation::factory()->count($count)->create([
                'user_id' => $user->id,
            ]);
            $totalSimulations += $count;
        }

        $this->command->info('✅ Created '.$totalSimulations.' simulations.');
    }
}
