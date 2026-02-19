<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Client;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Ensure a session is started so CSRF tokens are stable during feature tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create Password Grant Client for tests if Passport tables exist.
        // Some CI or local test DBs may not have the passport migrations applied,
        // so guard creation to avoid test failures.
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('oauth_clients')) {
                $client = Client::factory()->create([
                    'grant_types' => ['password', 'refresh_token'],
                    'revoked' => false,
                ]);

                // Make it available to the app
                config([
                    'passport.password_client.id' => $client->id,
                    'passport.password_client.secret' => $client->secret,
                ]);
            }
        } catch (\Throwable) {
            // If checking the schema or creating the client fails, skip gracefully.
        }
    }

    /**
     * Create an admin user for testing.
     *
     * This is useful for tests that require admin authorization.
     * Creates the admin role if it doesn't exist.
     *
     * @param  array  $attributes  Optional attributes to override defaults
     */
    protected function createAdminUser(array $attributes = []): User
    {
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrateur']
        );

        return User::factory()->create(array_merge([
            'role_id' => $adminRole->id,
        ], $attributes));
    }

    /**
     * Add CSRF token to request data.
     *
     * Laravel tests require CSRF tokens for POST/PUT/PATCH/DELETE requests.
     * This helper automatically includes the token in the data array.
     *
     * @param  array  $data  Request data
     * @return array Data with CSRF token included
     */
    protected function withCsrfToken(array $data = []): array
    {
        $token = csrf_token();

        // Also seed the session so the token matches what VerifyCsrfToken expects.
        $this->withSession(['_token' => $token]);

        return array_merge($data, [
            '_token' => $token,
        ]);
    }

    /**
     * Convenience wrappers that automatically inject a CSRF token into state-changing requests.
     */
    protected function postCsrf(string $uri, array $data = [], array $headers = [])
    {
        return $this->post($uri, $this->withCsrfToken($data), $headers);
    }

    protected function putCsrf(string $uri, array $data = [], array $headers = [])
    {
        return $this->put($uri, $this->withCsrfToken($data), $headers);
    }

    protected function patchCsrf(string $uri, array $data = [], array $headers = [])
    {
        return $this->patch($uri, $this->withCsrfToken($data), $headers);
    }

    protected function deleteCsrf(string $uri, array $data = [], array $headers = [])
    {
        return $this->delete($uri, $this->withCsrfToken($data), $headers);
    }
}
