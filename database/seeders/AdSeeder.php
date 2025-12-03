<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;

class AdSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating advertisements...');
        
        $count = 10;
        Ad::factory()->count($count)->create();
        
        $this->command->info('✅ Created ' . $count . ' advertisements.');
    }
}