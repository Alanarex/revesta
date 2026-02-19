<?php

namespace Database\Seeders;

use App\Models\FiscalIncomeRange;
use Illuminate\Database\Seeder;

class FiscalIncomeRangeSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating fiscal income ranges...');

        $count = 5;
        FiscalIncomeRange::factory()->count($count)->create();

        $this->command->info('✅ Created '.$count.' fiscal income ranges.');
    }
}
