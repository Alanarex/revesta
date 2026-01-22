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

        $this->command->info('Creating admin user...');
        
        // Create a custom user
        User::create([
            'first_name' => 'Alaa',
            'last_name' => 'Khalil',
            'email' => 'alaakhalil@gmail.com',
            'phone' => '0123456789',
            'address_id' => null,
            'civil_status' => 'single',
            'family_status' => 'without_children',
            'bio' => fake()->sentence(),
            'cookies_accepted' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => null,
            'role_id' => Role::where('name', 'admin')->first()?->id, // Assigning admin role
        ]);
        
        $this->command->info('✅ Created admin user (alaakhalil@gmail.com).');
    }
}