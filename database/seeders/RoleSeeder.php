<?php

// database/seeders/RoleSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating roles...');
        
        Role::insert([
            ['display_name' => 'Administrateur', 'name' => 'admin'],
            ['display_name' => 'Client', 'name' => 'client'],
            ['display_name' => 'Personnel', 'name' => 'staff'],
        ]);
        
        $this->command->info('✅ Created 3 roles.');
    }
}

