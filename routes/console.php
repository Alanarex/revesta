<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dispatch scheduled newsletters (useable from cron or scheduler)
Artisan::command('newsletter:dispatch-scheduled', function () {
    $service = app(\App\Services\NewsletterCampaignService::class);

    $campaigns = $service->getScheduled();

    foreach ($campaigns as $campaign) {
        if ($campaign->scheduled_at && $campaign->scheduled_at->lte(now())) {
            $service->sendNow($campaign->id);
            $this->info('Dispatched campaign '.$campaign->id);
        }
    }
})->describe('Dispatch scheduled newsletter campaigns');
