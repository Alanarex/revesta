<?php

namespace Tests\Feature\Controllers;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class NewsletterCampaignControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.newsletters.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_create_draft_and_redirect_to_edit()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->postCsrf(route('admin.newsletters.store'), [
            'title' => 'Test Campaign',
            'content' => '<p>Hello</p>',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('newsletter_campaigns', ['title' => 'Test Campaign']);
    }

    public function test_admin_send_now_dispatches_job()
    {
        Bus::fake();

        $admin = $this->createAdminUser();
        $campaign = NewsletterCampaign::factory()->draft()->create();

        $response = $this->actingAs($admin)
            ->postCsrf(route('admin.newsletters.send-now', $campaign));

        $response->assertRedirect();

        Bus::assertDispatched(SendNewsletterCampaignJob::class, function ($job) use ($campaign) {
            return $job->campaignId === $campaign->id;
        });
    }
}
