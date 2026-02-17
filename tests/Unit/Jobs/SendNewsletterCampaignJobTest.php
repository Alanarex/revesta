<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Repositories\NewsletterCampaignRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendNewsletterCampaignJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_emails_and_marks_sent()
    {
        Mail::fake();

        $campaign = NewsletterCampaign::factory()->draft()->create(['title' => 'T', 'content' => '<p>X</p>']);

        // Create verified subscribers
        Newsletter::factory()->count(3)->verified()->create();

        $job = new SendNewsletterCampaignJob($campaign->id);

        // Run handle with repo instance
        $repo = $this->app->make(NewsletterCampaignRepository::class);
        $job->handle($repo);

        // Assert mails queued
        Mail::assertQueued(\App\Mail\NewsletterCampaignMail::class, 3);

        $campaign->refresh();
        $this->assertEquals('sent', $campaign->status);
        $this->assertEquals(3, $campaign->sent_count);
    }
}
