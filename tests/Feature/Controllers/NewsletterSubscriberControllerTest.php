<?php

namespace Tests\Feature\Controllers;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_subscribers_list()
    {
        $admin = $this->createAdminUser();
        Newsletter::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.newsletter-subscribers.index'));
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_view_subscribers()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.newsletter-subscribers.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_verify_subscriber()
    {
        $admin = $this->createAdminUser();
        $sub = Newsletter::factory()->unverified()->create();

        $response = $this->actingAs($admin)->postCsrf(route('admin.newsletter-subscribers.verify', $sub));
        $response->assertRedirect();

        $sub->refresh();
        $this->assertNotNull($sub->verified_at);
    }

    public function test_admin_can_delete_subscriber_json()
    {
        $admin = $this->createAdminUser();
        $sub = Newsletter::factory()->create();

        $response = $this->actingAs($admin)->deleteCsrf(route('admin.newsletter-subscribers.destroy', $sub), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('newsletters', ['id' => $sub->id]);
    }
}
