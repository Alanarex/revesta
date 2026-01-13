<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create a custom admin user
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
