<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Simulation;
use App\Models\Aid;

class AidSimulationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating aid-simulation associations...');
        
        $simulations = Simulation::all();
        $aids = Aid::all();

        if ($simulations->isEmpty() || $aids->isEmpty()) {
            $this->command->warn('No simulations or aids found.');
            return;
        }

        $totalAssociations = 0;
        foreach ($simulations as $simulation) {
            $simulation->aids()->attach(
                $aids->random(2)->pluck('id')->toArray(),
                ['amount' => rand(100, 1000)]
            );
            $totalAssociations += 2;
        }
        
        $this->command->info('✅ Created ' . $totalAssociations . ' aid-simulation associations.');
    }
}