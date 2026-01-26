<?php

namespace App\Repositories;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TokenRepository
{
    public function createFor(User $user, array $abilities = [], ?\DateTimeInterface $expiresAt = null): array
    {
        $plain = bin2hex(random_bytes(40));
        $hash = hash('sha256', $plain);

        $token = ApiToken::create([
            'user_id' => $user->id,
            'name' => 'api-token',
            'token_hash' => $hash,
            'abilities' => $abilities ?: null,
            'expires_at' => $expiresAt ? Carbon::instance($expiresAt) : null,
        ]);

        return ['plain' => $plain, 'token' => $token];
    }

    public function findByPlain(string $plain): ?ApiToken
    {
        $hash = hash('sha256', $plain);

        $query = ApiToken::where('token_hash', $hash);

        $token = $query->first();

        if (! $token) {
            return null;
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return null;
        }

        return $token;
    }

    public function revoke(ApiToken $token): void
    {
        $token->delete();
    }
}
