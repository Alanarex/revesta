<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Housing;

class HousingSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating housing types...');
        
        $count = 10;
        Housing::factory()->count($count)->create();
        
        $this->command->info('✅ Created ' . $count . ' housing types.');
    }
}