<?php

namespace App\Console\Commands;

use App\Services\NewsletterCampaignService;
use Illuminate\Console\Command;

class DispatchScheduledNewsletters extends Command
{
    protected $signature = 'newsletter:dispatch-scheduled';

    protected $description = 'Dispatch scheduled newsletter campaigns whose scheduled_at is due.';

    public function __construct(protected NewsletterCampaignService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $campaigns = $this->service->getScheduled();

        foreach ($campaigns as $campaign) {
            if ($campaign->scheduled_at && $campaign->scheduled_at->lte(now())) {
                $this->service->sendNow($campaign->id);
                $this->info('Dispatched campaign '.$campaign->id);
            }
        }

        return 0;
    }
}
