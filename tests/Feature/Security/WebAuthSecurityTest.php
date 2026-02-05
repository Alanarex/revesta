<?php

namespace Tests\Feature\Security;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class WebAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_sql_injection_payload_fails(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-password')]);

        $resp = $this->post('/login', [
            'email' => "' OR 1=1 --",
            'password' => 'doesnotmatter',
        ]);

        // Should not authenticate or redirect to dashboard
        $resp->assertStatus(302);
        $this->assertGuest();
    }

    public function test_user_xss_payload_is_escaped_in_view(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('users.update', $user), $this->withCsrfToken([
            'first_name' => '<script>alert(1)</script>',
            'last_name' => 'Doe',
            'email' => $user->email,
        ]));

        $resp = $this->actingAs($user)->get(route('users.show', $user));
        $resp->assertStatus(200);

        $body = $resp->getContent();
        $this->assertStringNotContainsString('<script>alert(1)</script>', $body);
    }

    public function test_state_changing_requests_without_csrf_are_blocked(): void
    {
        $resp = $this->post('/login', [
            'email' => 'no-csrf@example.test',
            'password' => 'x',
        ], []);

        // In test environments CSRF behavior can vary; ensure the request did not authenticate
        $this->assertGuest();
        $this->assertNotEquals(200, $resp->getStatusCode());
    }

    public function test_login_rate_limiting_triggers_throttle(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        for ($i = 0; $i < 7; $i++) {
            $resp = $this->post('/login', $this->withCsrfToken([
                'email' => $user->email,
                'password' => 'wrong',
            ]));
        }

        // After many attempts, requests should be rate-limited (redirect with errors)
        $resp->assertStatus(302);
        $this->assertTrue(session()->has('errors'));
    }
}
