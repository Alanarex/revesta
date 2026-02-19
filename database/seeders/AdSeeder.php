<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating advertisements...');

        $count = 10;
        Ad::factory()->count($count)->create();

        $this->command->info('✅ Created '.$count.' advertisements.');
    }
}
