<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\BlogLikeController;
use App\Http\Controllers\BlogBookmarkController;
use App\Http\Controllers\BlogCommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API: Blog Routes
|--------------------------------------------------------------------------
|
| API endpoints for blog resources exposed under `/api/v1/blogs`. Public
| endpoints provide listing and detail views; authenticated endpoints (via
| `auth:api`) allow likes, bookmarks and comment management.
|
*/

Route::prefix('blogs')
    ->name('blogs.')
    ->group(function () {

        // Public reading routes
        Route::controller(BlogController::class)->group(function () {

            Route::get('/', 'index')
                ->name('index');

            Route::get('/{blog}', 'show')
                ->name('show');
        });

        // Authenticated user interactions
        Route::middleware('auth:api')->group(function () {

            // Like/Unlike blog
            Route::post('/{blog}/like/{model}/{modelId}', [BlogLikeController::class, 'like'])
                ->middleware('throttle:60,1')
                ->name('likes.toggle');

            // Bookmark blog
            Route::post('/{blog}/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])
                ->middleware('throttle:60,1')
                ->name('bookmarks.toggle');

            // Comment management
            Route::controller(BlogCommentController::class)->group(function () {

                Route::post('/{blog}/comments', 'store')
                    ->middleware('throttle:20,1')
                    ->name('comments.store');

                Route::delete('/comments/{comment}', 'destroy')
                    ->middleware('throttle:10,1')
                    ->name('comments.destroy');
            });
        });

        // Load more comments
        Route::get('/{blog}/comments/load-more', [BlogCommentController::class, 'loadMore'])
            ->name('comments.loadMore');

        // Guest-accessible comment replies
        Route::get('/{blog}/comments/{comment}/replies/load-more', [BlogCommentController::class, 'loadMoreReplies'])
            ->name('comments.loadMoreReplies');
    });