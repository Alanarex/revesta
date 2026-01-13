<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
            RoleSeeder::class,
        ]);

        // Only seed users in local or testing environments
        if (app()->environment('local')) {
            $this->call([
                AddressSeeder::class,
                FiscalIncomeRangeSeeder::class,
                AidSeeder::class,
                HousingSeeder::class,
                AdSeeder::class,
                ConditionSeeder::class,
                BlogSeeder::class,
                SimulationSeeder::class,
                AidSimulationSeeder::class,
                UserSeeder::class,
            ]);
        }
    }
}
