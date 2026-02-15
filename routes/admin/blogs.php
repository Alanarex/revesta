<?php

use App\Http\Controllers\BlogBookmarkController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogLikeController;
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

Route::controller(BlogCommentController::class)
    ->prefix('blogs')
    ->name('blogs.comments.')
    ->group(function () {
        Route::post('/{blog}/comments', 'store')
            ->middleware('throttle:20,1')
            ->name('store');

        Route::get('/{blog}/comments/{comment}/reply-form', 'replyForm')
            ->middleware('throttle:60,1')
            ->name('replyForm');

        Route::get('/{blog}/comments/{comment}/replies/load-more', 'loadMoreReplies')
            ->middleware('throttle:60,1')
            ->name('loadMoreReplies');

        Route::delete('/comments/{comment}', 'destroy')
            ->middleware('throttle:10,1')
            ->name('destroy');
    });

// Likes and bookmarks routes grouped under blogs prefix
Route::prefix('blogs')
    ->name('blogs.')
    ->group(function () {
        Route::post('/{blog}/like/{model}/{modelId}', [BlogLikeController::class, 'like'])
            ->middleware('throttle:60,1')
            ->name('like');

        Route::post('/{blog}/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])
            ->middleware('throttle:60,1')
            ->name('bookmarks.toggle');
    });


