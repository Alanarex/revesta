<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;

class ApiAuthTest extends TestCase
{
    public function test_login_and_me_and_logout_flow(): void
    {
        $password = 'secret-password';
        $user = User::factory()->create(['password' => bcrypt($password)]);

        // login
        $resp = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $resp->assertStatus(200);
        $data = $resp->json();
        $this->assertArrayHasKey('token', $data);

        $token = $data['token'];

        // me
        $me = $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$token}"]);
        $me->assertStatus(200);
        $this->assertEquals($user->id, $me->json('id'));

        // logout
        $logout = $this->postJson('/api/v1/auth/logout', [], ['Authorization' => "Bearer {$token}"]);
        $logout->assertStatus(200);

        // me now should be unauthenticated
        $this->getJson('/api/v1/auth/me', ['Authorization' => "Bearer {$token}"])->assertStatus(401);
    }

    public function test_login_invalid_credentials_returns_validation_error(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertStatus(422);
    }
}
