<?php

namespace Database\Seeders;

use App\Models\NewsletterCampaign;
use Illuminate\Database\Seeder;

class NewsletterCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 draft campaigns
        NewsletterCampaign::factory(5)->draft()->create();

        // Create 8 sent campaigns
        NewsletterCampaign::factory(8)->sent()->create();

        // Create 3 scheduled campaigns
        NewsletterCampaign::factory(3)->scheduled()->create();

        $this->command->info('Created 16 newsletter campaigns (5 drafts, 8 sent, 3 scheduled)');
    }
}
