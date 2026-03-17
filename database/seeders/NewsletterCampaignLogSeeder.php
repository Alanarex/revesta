<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignLog;
use Illuminate\Database\Seeder;

class NewsletterCampaignLogSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns   = NewsletterCampaign::all();
        $subscribers = Newsletter::all();

        if ($campaigns->isEmpty() || $subscribers->isEmpty()) {
            $this->command->warn('No campaigns or subscribers found.');

            return;
        }

        $this->command->info('Creating newsletter campaign logs...');

        $total = 0;
        foreach ($campaigns as $campaign) {
            $sample = $subscribers->random(min(rand(3, 8), $subscribers->count()));
            foreach ($sample as $subscriber) {
                NewsletterCampaignLog::factory()->create([
                    'campaign_id'   => $campaign->id,
                    'subscriber_id' => $subscriber->id,
                ]);
                $total++;
            }
        }

        $this->command->info("✅ Created {$total} campaign log entries.");
    }
}
