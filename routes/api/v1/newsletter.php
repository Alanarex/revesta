<?php

use App\Http\Controllers\Api\NewsletterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API: Newsletter Routes
|--------------------------------------------------------------------------
|
| API endpoints for newsletter subscriptions exposed under `/api/v1/newsletter`.
| Public endpoints available with rate limiting to prevent abuse. No authentication
| required for subscribing to the newsletter.
|
*/

Route::prefix('newsletter')
    ->middleware('throttle:5,1')
    ->name('newsletter.')
    ->controller(NewsletterController::class)
    ->group(function () {

        Route::post('/subscribe', 'subscribe')
            ->name('subscribe');

        Route::post('/verify', 'verify')
            ->name('verify');

        Route::post('/unsubscribe', 'unsubscribe')
            ->name('unsubscribe');

    });
