<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Address Routes
|--------------------------------------------------------------------------
|
| Administrative routes for managing addresses within the application.
| Includes listing (with datatables support), create/edit forms, and
| deletion endpoints. All routes are intended for admin use.
|
*/

Route::controller(AddressController::class)
    ->prefix('addresses')
    ->name('addresses.')
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

        Route::get('/{address}/edit', 'edit')
            ->name('edit');

        Route::put('/{address}', 'update')
            ->middleware('throttle:20,1')
            ->name('update');

        Route::delete('/{address}', 'destroy')
            ->middleware('throttle:10,1')
            ->name('destroy');
    });
