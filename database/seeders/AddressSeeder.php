<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating addresses...');

        $count = 40;
        Address::factory()->count($count)->create();

        $this->command->info('✅ Created '.$count.' addresses.');
    }
}
