<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Housing;

class HousingSeeder extends Seeder
{
    public function run()
    {
        Housing::factory()->count(10)->create();
    }
}