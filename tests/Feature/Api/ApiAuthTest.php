<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Client $passwordClient;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Password Grant Client for testing (Passport v12 uses `grant_types`)
        $this->passwordClient = Client::factory()->create([
            'grant_types' => ['password', 'refresh_token'],
            'revoked' => false,
        ]);

        config([
            'passport.password_client.id' => $this->passwordClient->id,
            'passport.password_client.secret' => $this->passwordClient->secret,
        ]);
    }

    public function user_can_login_and_receive_access_and_refresh_tokens(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        dd($response->json());

        $response->assertOk()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ]);
    }

    public function login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(400)
            ->assertJsonFragment([
                'error' => 'invalid_grant',
            ]);
    }

    public function login_fails_when_fields_are_missing(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function authenticated_user_can_access_me_endpoint(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetAccessToken($user);

        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);
    }

    public function unauthenticated_user_cannot_access_me(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    public function user_can_logout_and_token_is_revoked(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetAccessToken($user);

        $this->postJson('/api/v1/auth/logout', [], [
            'Authorization' => "Bearer {$token}",
        ])->assertNoContent();

        // Token must no longer work
        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(401);
    }

    public function refresh_token_can_issue_new_access_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $tokens = $this->loginAndGetTokens($user);

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokens['refresh_token'],
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->passwordClient->secret,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'refresh_token',
            ]);
    }

    public function revoked_token_cannot_be_used_even_if_header_is_present(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetAccessToken($user);

        // Manually revoke
        $user->tokens()->first()->revoke();

        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(401);
    }

    /* -----------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------------*/

    protected function loginAndGetTokens(User $user): array
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $response->json();
    }

    protected function loginAndGetAccessToken(User $user): string
    {
        return $this->loginAndGetTokens($user)['access_token'];
    }
}
