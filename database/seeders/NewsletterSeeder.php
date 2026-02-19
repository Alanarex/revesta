<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 verified subscribers
        Newsletter::factory(50)->verified()->create();

        // Create 30 unverified subscribers
        Newsletter::factory(30)->unverified()->create();

        $this->command->info('Created 80 newsletter subscribers (50 verified, 30 unverified)');
    }
}
