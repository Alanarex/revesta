<?php

namespace Tests\Unit\Services;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterCampaign;
use App\Services\NewsletterCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class NewsletterCampaignServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_now_dispatches_job()
    {
        Bus::fake();

        $campaign = NewsletterCampaign::factory()->draft()->create();

        $service = $this->app->make(NewsletterCampaignService::class);

        $result = $service->sendNow($campaign->id);

        $this->assertTrue($result['success']);

        Bus::assertDispatched(SendNewsletterCampaignJob::class, function ($job) use ($campaign) {
            return $job->campaignId === $campaign->id;
        });
    }
}
