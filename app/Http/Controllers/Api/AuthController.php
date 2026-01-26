<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginApiRequest;
use App\Repositories\UserRepository;
use App\Repositories\TokenRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository,
        protected TokenRepository $tokenRepository
    ) {
    }

    public function login(LoginApiRequest $request)
    {
        $data = $request->validated();

        $user = $this->userRepository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        $pair = $this->tokenRepository->createFor($user, ['*'], now()->addDays(30));

        return response()->json([
            'token' => $pair['plain'],
            'token_type' => 'Bearer',
            'expires_at' => $pair['token']->expires_at?->toDateTimeString(),
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $token = $request->attributes->get('api_token');

        if ($token) {
            $this->tokenRepository->revoke($token);
        }

        return response()->json(['message' => 'Logged out']);
    }
}
