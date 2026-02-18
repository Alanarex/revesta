<?php

namespace Tests\Feature\Commands;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterCampaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class DispatchScheduledNewslettersTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_only_due_campaigns()
    {
        Bus::fake();

        $due = NewsletterCampaign::factory()->scheduled()->create(['scheduled_at' => now()->subMinute()]);
        $future = NewsletterCampaign::factory()->scheduled()->create(['scheduled_at' => now()->addDay()]);

        Artisan::call('newsletter:dispatch-scheduled');

        Bus::assertDispatched(SendNewsletterCampaignJob::class, function ($job) use ($due) {
            return $job->campaignId === $due->id;
        });

        Bus::assertNotDispatched(SendNewsletterCampaignJob::class, function ($job) use ($future) {
            return $job->campaignId === $future->id;
        });
    }
}
