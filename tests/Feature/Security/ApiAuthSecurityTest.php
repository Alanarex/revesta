<?php

namespace Tests\Feature\Security;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Repositories\TokenRepository;
use Carbon\Carbon;

class ApiAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_requires_valid_token(): void
    {
        $user = User::factory()->create();

        // create a token and ensure it works
        $pair = app(TokenRepository::class)->createFor($user, ['*'], Carbon::now()->addDays(1));
        $plain = $pair['plain'];

        $resp = $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"]);
        $resp->assertStatus(200);

        // invalid token fails
        $this->getJson('/api/v1/auth/me', ['Authorization' => 'Bearer invalid-token'])->assertStatus(401);
    }

    public function test_revoked_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $repo = app(TokenRepository::class);
        $pair = $repo->createFor($user, ['*'], Carbon::now()->addDays(1));
        $plain = $pair['plain'];
        $token = $pair['token'];

        // token works initially
        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"])->assertStatus(200);

        // revoke
        $repo->revoke($token);

        // now token is rejected
        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"])->assertStatus(401);
    }

    public function test_expired_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $repo = app(TokenRepository::class);
        $pair = $repo->createFor($user, ['*'], Carbon::now()->subHour());
        $plain = $pair['plain'];

        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"])->assertStatus(401);
    }
}
