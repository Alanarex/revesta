<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aid;

class AidSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating aids...');
        
        $count = 8;
        Aid::factory()->count($count)->create();
        
        $this->command->info('✅ Created ' . $count . ' aids.');
    }
}