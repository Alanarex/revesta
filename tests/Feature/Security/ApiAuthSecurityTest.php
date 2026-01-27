<?php

namespace Tests\Feature\Security;

use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class ApiAuthSecurityTest extends TestCase
{
    public function protected_route_requires_a_valid_access_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetAccessToken($user);

        // valid token works
        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(200);

        // invalid token fails
        $this->getJson('/api/v1/auth/me', [
            'Authorization' => 'Bearer invalid-token',
        ])->assertStatus(401);
    }

    public function revoked_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetAccessToken($user);

        // revoke via Passport
        $user->tokens()->first()->revoke();

        // token is now invalid
        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(401);
    }

    public function expired_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetAccessToken($user);

        // Force expiry (simulate passage of time)
        Carbon::setTestNow(Carbon::now()->addYears(2));

        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(401);

        Carbon::setTestNow(); // reset
    }

    /* -------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------*/
    protected function loginAndGetAccessToken(User $user): string
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $response->json('access_token');
    }
}
