<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Routes for the user dashboard. These routes are protected by
| `auth` and `verified` middleware and provide the main logged-in landing
| area for users.
|
*/

Route::controller(DashboardController::class)
    ->prefix('')
    ->name('dashboard.')
    ->group(function () {

        Route::middleware(['auth', 'verified'])
            ->group(function () {

                Route::get('/', 'index')
                    ->name('index');

            });
    });