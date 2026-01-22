<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating addresses...');
        
        $count = 40;
        Address::factory()->count($count)->create();
        
        $this->command->info('✅ Created ' . $count . ' addresses.');
    }
}