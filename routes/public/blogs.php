<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogLikeController;
use App\Http\Controllers\BlogBookmarkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Blog Routes
|--------------------------------------------------------------------------
|
| Public-facing blog routes: listing and reading blogs are available to
| all visitors. Authenticated users gain additional interactions such as
| comments, likes and bookmarks. Admin CRUD and moderation live under
| `routes/admin/blogs.php`.
|
*/

// Public blog viewing (no auth required)
Route::prefix('blogs')
    ->name('blogs.')
    ->group(function () {

        Route::middleware(['auth', 'verified'])->group(function () {

            Route::controller(BlogCommentController::class)
                ->group(function () {

                    // Comment management
                    Route::post('/{blog}/comments', 'store')
                        ->middleware('throttle:20,1')
                        ->name('comments.store');

                    Route::get('/{blog}/comments/load-more', 'loadMore')
                        ->name('comments.loadMore');

                    Route::delete('/comments/{comment}', 'destroy')
                        ->middleware('throttle:10,1')
                        ->name('comments.destroy');
                });

            // Likes and bookmarks (use blog id in URL like API)
            Route::post('/{blog}/likes/toggle', [BlogLikeController::class, 'toggle'])
                ->middleware('throttle:60,1')
                ->name('likes.toggle');

            Route::post('/{blog}/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])
                ->middleware('throttle:60,1')
                ->name('bookmarks.toggle');

            // Guest-accessible comment replies
            Route::get('/{blog}/comments/{comment}/replies/load-more', [BlogCommentController::class, 'loadMoreReplies'])
                ->name('comments.loadMoreReplies');
        });
    });
