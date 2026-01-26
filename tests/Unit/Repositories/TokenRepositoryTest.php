<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\TokenRepository;
use Illuminate\Support\Carbon;

class TokenRepositoryTest extends TestCase
{
    public function test_create_and_find_token_success(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $repo = new TokenRepository();

        $pair = $repo->createFor($user, ['read'], Carbon::now()->addMinutes(10));

        $this->assertArrayHasKey('plain', $pair);
        $this->assertArrayHasKey('token', $pair);

        $found = $repo->findByPlain($pair['plain']);

        $this->assertNotNull($found);
        $this->assertEquals($pair['token']->id, $found->id);
    }

    public function test_revoked_or_expired_token_not_found(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $repo = new TokenRepository();

        $pair = $repo->createFor($user, ['read'], Carbon::now()->subMinutes(10));

        // expired
        $this->assertNull($repo->findByPlain($pair['plain']));

        // create valid and then revoke
        $pair2 = $repo->createFor($user, ['read'], Carbon::now()->addMinutes(10));
        $token = $pair2['token'];

        $repo->revoke($token);

        $this->assertNull($repo->findByPlain($pair2['plain']));
    }
}
