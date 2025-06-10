<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FiscalIncomeRange;

class FiscalIncomeRangeSeeder extends Seeder
{
    public function run()
    {
        FiscalIncomeRange::factory()->count(5)->create();
    }
}