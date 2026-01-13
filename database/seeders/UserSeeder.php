<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating random users...');
        
        $randomUserCount = 10;
        User::factory()->count($randomUserCount)->create([
            'bio' => fake()->sentence(),
        ]);
        $this->command->info('✅ Created ' . $randomUserCount . ' random users.');

    }
}