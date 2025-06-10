<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aid;

class AidSeeder extends Seeder
{
    public function run()
    {
        Aid::factory()->count(8)->create();
    }
}