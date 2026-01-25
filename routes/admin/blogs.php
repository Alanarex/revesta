<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Blog Management Routes
|--------------------------------------------------------------------------
|
| Routes for admin blog dashboard, CRUD operations, and moderation.
| All routes require authentication and admin authorization.
|
| Route Structure:
| - Dashboard: GET /
| - Create & Store: GET /create, POST /
| - View & Edit: GET /{blog}, GET /{blog}/edit
| - Update & Publish: PUT /{blog}, POST /{blog}/publish
| - Delete: DELETE /{blog}
| - Moderation: POST /{blog}/approve, POST /{blog}/reject
|
*/

Route::prefix('blogs')
    ->name('blogs.')
    ->group(function () {

        // Dashboard and blog listing
        Route::get('/', [BlogController::class, 'index'])
            ->name('index');

        // Blog creation
        Route::get('/create', [BlogController::class, 'create'])
            ->name('create');
        Route::post('/', [BlogController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('store');

        // Blog viewing and editing
        Route::get('/{blog}', [BlogController::class, 'show'])
            ->name('show');
        Route::get('/{blog}/edit', [BlogController::class, 'edit'])
            ->name('edit');

        // Blog updates
        Route::put('/{blog}', [BlogController::class, 'update'])
            ->middleware('throttle:20,1')
            ->name('update');
        Route::post('/{blog}/publish', [BlogController::class, 'publish'])
            ->middleware('throttle:10,1')
            ->name('publish');

        // Blog deletion
        Route::delete('/{blog}', [BlogController::class, 'destroy'])
            ->middleware('throttle:10,1')
            ->name('destroy');

        // Admin moderation: approve and reject
        Route::post('/{blog}/approve', [BlogController::class, 'approve'])
            ->name('approve');
        Route::post('/{blog}/reject', [BlogController::class, 'reject'])
            ->name('reject');
    });


