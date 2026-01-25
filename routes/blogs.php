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
| Routes for public blog viewing and user interactions.
| Note: Admin CRUD operations are in routes/admin/blogs.php
|
| Unauthenticated Routes:
| - Blog viewing and listing
|
| Authenticated Routes:
| - Comments: create, delete, load more
| - Likes: toggle
| - Bookmarks: toggle
|
*/

// Public blog viewing (no auth required)
Route::prefix('blogs')->name('blogs.')->group(function () {
    // TODO: Add public blog listing and individual blog viewing routes
    // Route::get('/', ...)->name('index');
    // Route::get('/{blog}', ...)->name('show');

    // Authenticated user interactions
    Route::middleware(['auth', 'verified'])->group(function () {

        // Comment management
        Route::post('/{blog}/comments', [BlogCommentController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('comments.store');
        Route::get('/{blog}/comments/load-more', [BlogCommentController::class, 'loadMore'])
            ->name('comments.loadMore');
        Route::delete('/comments/{comment}', [BlogCommentController::class, 'destroy'])
            ->middleware('throttle:10,1')
            ->name('comments.destroy');

        // Likes and bookmarks
        Route::post('/likes/toggle', [BlogLikeController::class, 'toggle'])
            ->middleware('throttle:60,1')
            ->name('likes.toggle');
        Route::post('/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])
            ->middleware('throttle:60,1')
            ->name('bookmarks.toggle');
    });

    // Guest-accessible comment replies
    Route::get('/{blog}/comments/{comment}/replies/load-more', [BlogCommentController::class, 'loadMoreReplies'])
        ->name('comments.loadMoreReplies');
});

