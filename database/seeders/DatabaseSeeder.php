<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
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
                AdImageSeeder::class,
                ConditionSeeder::class,
                UserSeeder::class,
                AdminUserSeeder::class,
                BlogSeeder::class,
                SimulationSeeder::class,
                RenovationWorkSeeder::class,
                RenovationWorkSimulationSeeder::class,
                AidSimulationSeeder::class,
                NotificationSeeder::class,
                NewsletterSeeder::class,
                NewsletterCampaignSeeder::class,
                NewsletterCampaignLogSeeder::class,
            ]);
        }
    }
}
