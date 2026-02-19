<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends Controller
{
    /**
     * Login using Passport OAuth2 Password Grant
     * This is just a proxy to /oauth/token
     *
     * @group Authentication
     *
     * @bodyParam email string required The user's email. Example: admin@gmail.com
     * @bodyParam password string required The user's password. Example: password
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request): JsonResponse|SymfonyResponse
    {

        $client = config('passport.password_client');

        if (! $client || empty($client['id']) || empty($client['secret'])) {
            return response()->json([
                'message' => 'Password grant client not configured',
            ], 500);
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => $client['id'],
            'client_secret' => $client['secret'],
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '',
        ];

        $proxy = SymfonyRequest::create(
            '/oauth/token',
            'POST',
            $data,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            ]
        );

        return app()->handle($proxy);
    }

    /**
     * Get authenticated user
     *
     * @group Authentication
     *
     * @authenticated
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout = revoke only current access token
     *
     * @group Authentication
     *
     * @authenticated
     *
     * @response 204
     */
    public function logout(Request $request): SymfonyResponse
    {
        $token = $request->user()->token();

        $token->revoke();

        return response()->noContent();
    }
}
