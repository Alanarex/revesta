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
        // Create admin users
        $admins = [
            [
                'first_name' => 'Toto',
                'last_name' => 'TITI',
                'email' => 'tototiti@gmail.com',
                'phone' => '0123456789',
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@gmail.com',
                'phone' => '0111111111',
            ],
            [
                'first_name' => 'Modérateur',
                'last_name' => 'Principal',
                'email' => 'moderator@gmail.com',
                'phone' => '0222222222',
            ],
            [
                'first_name' => 'Gestionnaire',
                'last_name' => 'Contenu',
                'email' => 'manager@gmail.com',
                'phone' => '0333333333',
            ],
        ];

        $adminRole = Role::where('name', 'admin')->first();

        foreach ($admins as $adminData) {
            User::create([
                'first_name' => $adminData['first_name'],
                'last_name' => $adminData['last_name'],
                'email' => $adminData['email'],
                'phone' => $adminData['phone'],
                'address_id' => null,
                'civil_status' => 'single',
                'family_status' => 'without_children',
                'bio' => fake()->sentence(),
                'cookies_accepted' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role_id' => $adminRole?->id,
            ]);
            $this->command->info("✅ Created admin user ({$adminData['email']}).");
        }
    }
}
