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
        // Create 10 random users
        User::factory()->count(10)->create();

        // Create a custom user
        User::create([
            'first_name' => 'Alaa',
            'last_name' => 'Khalil',
            'email' => 'alaakhalil@gmail.com',
            'phone' => '0123456789',
            'address_id' => null,
            'civil_status' => 'single',
            'family_status' => 'without_children',
            'cookies_accepted' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => null,
            'role_id' => Role::where('name', 'admin')->first()?->id, // Assigning admin role
        ]);
    }
}