<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\TokenRepository;
use Illuminate\Support\Facades\Auth;

class AuthenticateApiToken
{
    public function __construct(protected TokenRepository $tokenRepository)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $plain = substr($header, 7);

        $token = $this->tokenRepository->findByPlain($plain);

        if (! $token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = $token->user;

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn() => $user);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
