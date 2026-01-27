<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class AuthController extends Controller
{
    /**
     * Login using Passport OAuth2 Password Grant
     * This is just a proxy to /oauth/token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $client = config('passport.password_client');

        if (!$client || empty($client['id']) || empty($client['secret'])) {
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
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout = revoke only current access token
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        return response()->noContent();
    }
}
