<?php

use App\Http\Controllers\ConditionsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Conditions Routes
|--------------------------------------------------------------------------
|
| Routes for managing application conditions (admin area). Provides listing
| and update endpoints used by administrators to alter application-wide
| condition settings.
|
*/

Route::controller(ConditionsController::class)
    ->prefix('conditions')
    ->name('conditions.')
    ->group(function () {

        Route::get('/', 'index')
            ->name('index');

        Route::post('/update', 'update')
            ->name('update');
    });
