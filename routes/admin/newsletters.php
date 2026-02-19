<?php

use App\Http\Controllers\NewsletterCampaignController;
use App\Http\Controllers\NewsletterSubscriberController;
use Illuminate\Support\Facades\Route;

Route::prefix('newsletter-subscribers')
    ->name('newsletter-subscribers.')
    ->controller(NewsletterSubscriberController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'list')->name('list');
        Route::post('/{subscriber}/verify', 'verify')->name('verify');
        Route::delete('/{subscriber}', 'destroy')->name('destroy');
    });

Route::prefix('newsletters')
    ->name('newsletters.')
    ->controller(NewsletterCampaignController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{campaign}', 'show')->name('show');
        Route::get('/{campaign}/edit', 'edit')->name('edit');
        Route::put('/{campaign}', 'update')->name('update');
        Route::delete('/{campaign}', 'destroy')->name('destroy');

        Route::post('/{campaign}/send-now', 'sendNow')->name('send-now');
        Route::get('/{campaign}/schedule', 'scheduleForm')->name('schedule-form');
        Route::post('/{campaign}/schedule', 'schedule')->name('schedule');
        Route::post('/{campaign}/cancel-schedule', 'cancelSchedule')->name('cancel-schedule');

        // Filter routes
        Route::get('/drafts', 'drafts')->name('drafts');
        Route::get('/sent', 'sent')->name('sent');
    });
