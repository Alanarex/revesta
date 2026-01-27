<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
|
| Routes for viewing and managing user profiles. Editing and sensitive
| profile actions are protected by `auth` and `verified` middleware; public
| profile viewing remains accessible.
|
*/

Route::controller(ProfileController::class)
    ->prefix('profile')
    ->name('profile.')
    ->group(function () {

        Route::middleware(['auth', 'verified'])
            ->group(function () {

                Route::get('/', 'edit')
                    ->name('edit');

                Route::patch('/', 'update')
                    ->name('update');

                Route::put('/password', 'updatePassword')
                    ->name('password.update');

                Route::delete('/', 'destroy')
                    ->name('destroy');

            });

        Route::get('/{userId}', 'show')
            ->name('show');

    });
