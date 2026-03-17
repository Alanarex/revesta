<?php

use App\Http\Controllers\Api\SimulationController;
use Illuminate\Support\Facades\Route;

Route::prefix('simulations')
    ->name('simulations.')
    ->middleware('throttle:30,1')
    ->controller(SimulationController::class)
    ->group(function () {
        Route::post('/', 'store')->name('store');
    });
