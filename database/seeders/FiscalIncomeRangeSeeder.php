<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FiscalIncomeRange;

class FiscalIncomeRangeSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating fiscal income ranges...');
        
        $count = 5;
        FiscalIncomeRange::factory()->count($count)->create();
        
        $this->command->info('✅ Created ' . $count . ' fiscal income ranges.');
    }
}