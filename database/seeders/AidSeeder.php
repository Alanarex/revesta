<?php

namespace Database\Seeders;

use App\Models\Aid;
use Illuminate\Database\Seeder;

class AidSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating aids...');

        $count = 8;
        Aid::factory()->count($count)->create();

        $this->command->info('✅ Created '.$count.' aids.');
    }
}
