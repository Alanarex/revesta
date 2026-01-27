<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API: Auth Routes
|--------------------------------------------------------------------------
|
| Authentication endpoints for the API. Includes `login` (public) and
| `me`/`logout` protected by `auth:sanctum`. Registered under the
| `/api/v1/auth` prefix by the API loader.
|
*/

Route::controller(AuthController::class)
    ->prefix('auth')
    ->name('auth.')
    ->group(function () {

        Route::post('/login', 'login')
            ->name('login');

        Route::middleware('auth:sanctum')
            ->group(function () {

                Route::get('/me', 'me')
                    ->name('me');

                Route::post('/logout', 'logout')
                    ->name('logout');
            });
    });
