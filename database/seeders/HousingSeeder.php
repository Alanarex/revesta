<?php

namespace Database\Seeders;

use App\Models\Housing;
use Illuminate\Database\Seeder;

class HousingSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating housing types...');

        $count = 10;
        $housings = Housing::factory()->count($count)->create();

        // Attach addresses via polymorphic relationship
        foreach ($housings as $housing) {
            if ($housing->address_id) {
                $housing->addresses()->attach($housing->address_id, [
                    'type' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Created '.$count.' housing types.');
    }
}
