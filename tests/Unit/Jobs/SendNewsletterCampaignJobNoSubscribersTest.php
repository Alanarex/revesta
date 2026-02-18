<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterCampaign;
use App\Repositories\NewsletterCampaignRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendNewsletterCampaignJobNoSubscribersTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_marks_sent_with_zero_count_when_no_subscribers()
    {
        Mail::fake();

        $campaign = NewsletterCampaign::factory()->draft()->create();

        $job = new SendNewsletterCampaignJob($campaign->id);

        $repo = $this->app->make(NewsletterCampaignRepository::class);
        $job->handle($repo);

        $campaign->refresh();
        $this->assertEquals('sent', $campaign->status);
        $this->assertEquals(0, $campaign->sent_count);
    }
}
