<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\BlogLikeController;
use App\Http\Controllers\BlogBookmarkController;
use App\Http\Controllers\BlogCommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Blog Routes
|--------------------------------------------------------------------------
|
| Routes for public blog reading and user interactions.
| Blog listing and viewing are public, interactions require authentication.
|
| Public Routes:
| - Blog listing and searching
| - Blog viewing with full details
| - Comment reply pagination
|
| Authenticated Routes:
| - Like/Unlike blogs
| - Create and delete comments
| - Load more comments
|
*/

Route::prefix('blogs')
    ->name('blogs.')
    ->group(function () {

        // Public reading routes
        Route::get('/', [BlogController::class, 'index'])
            ->name('index');
        Route::get('/{blog}', [BlogController::class, 'show'])
            ->name('show');

        // Authenticated user interactions
        Route::middleware('auth:sanctum')->group(function () {

            // Like/Unlike blog
            Route::post('/{blog}/likes/toggle', [BlogLikeController::class, 'toggle'])
                ->middleware('throttle:60,1')
                ->name('likes.toggle');

            // Bookmark blog
            Route::post('/{blog}/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])
                ->middleware('throttle:60,1')
                ->name('bookmarks.toggle');

            // Comment management
            Route::post('/{blog}/comments', [BlogCommentController::class, 'store'])
                ->middleware('throttle:20,1')
                ->name('comments.store');
            Route::delete('/comments/{comment}', [BlogCommentController::class, 'destroy'])
                ->middleware('throttle:10,1')
                ->name('comments.destroy');

            // Load more comments
            Route::get('/{blog}/comments/load-more', [BlogCommentController::class, 'loadMore'])
                ->name('comments.loadMore');
        });

        // Guest-accessible comment replies
        Route::get('/{blog}/comments/{comment}/replies/load-more', [BlogCommentController::class, 'loadMoreReplies'])
            ->name('comments.loadMoreReplies');
    });