<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public User Routes
|--------------------------------------------------------------------------
|
| Public-facing user routes for viewing and editing user profiles (now
| named "users" routes). Edit/update routes are protected by auth and use
| the FormRequest authorization checks already defined in the app.
|
*/

Route::prefix('users')
    ->name('users.')
    ->group(function () {

        // All user pages require authentication
        Route::middleware(['auth'])->group(function () {
            // View user
            Route::get('/{user}', [UserController::class, 'show'])
                ->name('show');

            // Editing/updating a user — FormRequests handle authorization
            Route::get('/{user}/edit', [UserController::class, 'edit'])
                ->name('edit');

            Route::match(['put', 'patch'], '/{user}', [UserController::class, 'update'])
                ->name('update');

            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])
                ->name('reset-password');

            Route::delete('/{user}', [UserController::class, 'destroy'])
                ->name('destroy');
        });
    });
