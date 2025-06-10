<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Simulation;
use App\Models\Aid;

class AidSimulationSeeder extends Seeder
{
    public function run()
    {
        $simulations = Simulation::all();
        $aids = Aid::all();

        foreach ($simulations as $simulation) {
            $simulation->aids()->attach(
                $aids->random(2)->pluck('id')->toArray(),
                ['amount' => rand(100, 1000)]
            );
        }
    }
}