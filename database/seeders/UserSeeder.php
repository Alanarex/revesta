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
        $users = User::factory()->count($randomUserCount)->create([
            'bio' => fake()->sentence(),
        ]);
        
        // Attach addresses via polymorphic relationship
        foreach ($users as $user) {
            if ($user->address_id) {
                $user->addresses()->attach($user->address_id, [
                    'type' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('✅ Created ' . $randomUserCount . ' random users.');

    }
}