<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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

// Schedule the dispatch command via Laravel's scheduler.
// When using the scheduler, add a single cron entry on the server to run
// `php artisan schedule:run` every minute. See docs/newsletters/README.md
// for server cron examples.
Schedule::command('newsletter:dispatch-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

