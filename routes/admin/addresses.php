<?php

use App\Http\Controllers\AddressController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Address Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to address management:
| - Admin-only address listing with datatable
| - Address creation and editing
| - Address deletion
|
*/

Route::prefix('addresses')
    ->name('addresses.')
    ->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/list', [AddressController::class, 'list'])->name('list');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->middleware('throttle:10,1')->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->middleware('throttle:20,1')->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->middleware('throttle:10,1')->name('destroy');
    });
