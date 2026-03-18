<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->controller(SimulationController::class)
    ->prefix('simulations')
    ->name('simulations.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{simulation}', 'show')->name('show');
    });
