<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AddressSeeder::class,
            FiscalIncomeRangeSeeder::class,
            AidSeeder::class,
            HousingSeeder::class,
            AdSeeder::class,
            ConditionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SimulationSeeder::class,
            AidSimulationSeeder::class,
        ]);
    }
}
