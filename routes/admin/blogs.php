<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Blog Management
|--------------------------------------------------------------------------
|
| Admin CRUD and moderation routes for blog management. These endpoints
| are intended for administrative users and include creation, editing,
| publishing, deletion and moderation actions.
|
*/

Route::controller(BlogController::class)
    ->prefix('blogs')
    ->name('blogs.')
    ->group(function () {

        // Dashboard and blog listing
        Route::get('/', 'index')
            ->name('index');

        // Blog creation
        Route::get('/create', 'create')
            ->name('create');

        Route::post('/', 'store')
            ->middleware('throttle:10,1')
            ->name('store');

        // Blog viewing and editing
        Route::get('/{blog}', 'show')
            ->name('show');

        Route::get('/{blog}/edit', 'edit')
            ->name('edit');

        // Blog updates
        Route::put('/{blog}', 'update')
            ->middleware('throttle:20,1')
            ->name('update');

        Route::post('/{blog}/publish', 'publish')
            ->middleware('throttle:10,1')
            ->name('publish');

        // Blog deletion
        Route::delete('/{blog}', 'destroy')
            ->middleware('throttle:10,1')
            ->name('destroy');

        // Admin moderation: approve and reject
        Route::post('/{blog}/approve', 'approve')
            ->name('approve');

        Route::post('/{blog}/reject')
            ->name('reject');
    });


