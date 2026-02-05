<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin User Routes
|--------------------------------------------------------------------------
|
| Administrative routes for managing users within the application.
|
*/

Route::controller(UserController::class)
    ->prefix('users')
    ->name('users.')
    ->group(function () {

        Route::get('/', 'index')
            ->name('index');

        Route::get('/list', 'list')
            ->name('list');

        Route::get('/create', 'create')
            ->name('create');

        Route::post('/', 'store')
            ->middleware('throttle:10,1')
            ->name('store');

        Route::get('/{user}/edit', 'edit')
            ->name('edit');

        Route::get('/{user}', 'show')
            ->name('show');

        Route::put('/{user}', 'update')
            ->middleware('throttle:20,1')
            ->name('update');

        Route::delete('/{user}', 'destroy')
            ->middleware('throttle:10,1')
            ->name('destroy');
    });
