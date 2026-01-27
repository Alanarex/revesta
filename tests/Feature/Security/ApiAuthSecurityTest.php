<?php

namespace Tests\Feature\Security;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Carbon\Carbon;

class ApiAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_requires_valid_token(): void
    {
        $user = User::factory()->create();

        // create a token via Sanctum and ensure it works
        $new = $user->createToken('test', ['*']);
        $plain = $new->plainTextToken;
        $tokenModel = $new->accessToken ?? ($new->token ?? null);

        if ($tokenModel) {
            $tokenModel->expires_at = Carbon::now()->addDays(1);
            $tokenModel->save();
        }

        $resp = $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"]);
        $resp->assertStatus(200);

        // invalid token fails
        $this->getJson('/api/v1/auth/me', ['Authorization' => 'Bearer invalid-token'])->assertStatus(401);
    }

    public function test_revoked_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $new = $user->createToken('test', ['*']);
        $plain = $new->plainTextToken;
        $tokenModel = $new->accessToken ?? ($new->token ?? null);

        // token works initially
        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"])->assertStatus(200);

        // revoke
        if ($tokenModel) {
            $tokenModel->delete();
        }

        // now token is rejected
        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"])->assertStatus(401);
    }

    public function test_expired_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $new = $user->createToken('test', ['*']);
        $plain = $new->plainTextToken;
        $tokenModel = $new->accessToken ?? ($new->token ?? null);

        if ($tokenModel) {
            $tokenModel->expires_at = Carbon::now()->subHour();
            $tokenModel->save();
        }

        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$plain}"])->assertStatus(401);
    }
}
