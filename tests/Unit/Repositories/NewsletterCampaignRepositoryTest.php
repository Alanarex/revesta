<?php

namespace Tests\Unit\Repositories;

use App\Models\NewsletterCampaign;
use App\Repositories\NewsletterCampaignRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterCampaignRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_scheduled_and_sent()
    {
        $campaign = NewsletterCampaign::factory()->draft()->create();
        $repo = new NewsletterCampaignRepository;

        $this->assertTrue($repo->markAsScheduled($campaign->id, new \DateTime('+1 hour')));
        $campaign->refresh();
        $this->assertEquals('scheduled', $campaign->status);

        $this->assertTrue($repo->markAsSent($campaign->id, 5));
        $campaign->refresh();
        $this->assertEquals('sent', $campaign->status);
        $this->assertEquals(5, $campaign->sent_count);
    }
}
