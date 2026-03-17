<?php

namespace Database\Seeders;

use App\Models\RenovationWork;
use App\Models\Simulation;
use Illuminate\Database\Seeder;

class RenovationWorkSimulationSeeder extends Seeder
{
    public function run(): void
    {
        $simulations = Simulation::all();
        $works = RenovationWork::all();

        if ($simulations->isEmpty() || $works->isEmpty()) {
            $this->command->warn('No simulations or renovation works found.');

            return;
        }

        $this->command->info('Attaching renovation works to simulations...');

        $total = 0;
        foreach ($simulations as $simulation) {
            $count = rand(1, min(3, $works->count()));
            $simulation->works()->syncWithoutDetaching(
                $works->random($count)->pluck('id')->toArray()
            );
            $total += $count;
        }

        $this->command->info("✅ Attached {$total} renovation work entries to simulations.");
    }
}
