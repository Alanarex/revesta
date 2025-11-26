<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;
use App\Models\Aid;

class ConditionSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating conditions for aids...');
        
        $aids = Aid::all();
        
        if ($aids->isEmpty()) {
            $this->command->warn('No aids found! Please run AidSeeder first.');
            return;
        }

        $totalConditions = 0;
        foreach ($aids as $aid) {
            $count = 2;
            Condition::factory()->count($count)->create([
                'aid_id' => $aid->id,
            ]);
            $totalConditions += $count;
        }
        
        $this->command->info('✅ Created ' . $totalConditions . ' conditions.');
    }
}