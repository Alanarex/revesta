<?php

namespace Tests\Feature\Controllers;

use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterCampaignControllerPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_send_now()
    {
        $user = User::factory()->create();
        $campaign = NewsletterCampaign::factory()->draft()->create();

        $this->actingAs($user)
            ->postCsrf(route('admin.newsletters.send-now', $campaign))
            ->assertStatus(403);
    }
}
