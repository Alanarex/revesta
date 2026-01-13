<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogLikeController;
use App\Http\Controllers\BlogBookmarkController;
use App\Http\Controllers\AdminBlogController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to the blog system including:
| - Public blog viewing (index, show)
| - Blog management (create, edit, update, delete)
| - Comments, likes, and bookmarks
| - Admin blog management (approval, rejection, bulk actions)
|
*/

// Public blog routes (guests and authenticated users)
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');

// Authenticated blog routes
// Blog management routes (specific routes BEFORE wildcard)
Route::prefix('blogs')->name('blogs.')->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::middleware([IsAdmin::class])->group(function () {
            Route::get('/create', [BlogController::class, 'create'])->name('create');
            Route::post('/', [BlogController::class, 'store'])->middleware('throttle:10,1')->name('store');
            Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('edit');
            Route::put('/{blog}', [BlogController::class, 'update'])->middleware('throttle:20,1')->name('update');
            Route::post('/{blog}/publish', [BlogController::class, 'publish'])->middleware('throttle:10,1')->name('publish');
            Route::delete('/{blog}', [BlogController::class, 'destroy'])->middleware('throttle:10,1')->name('destroy');
        });

        // Comments
        Route::post('/{blog}/comments', [BlogCommentController::class, 'store'])->middleware('throttle:20,1')->name('comments.store');
        Route::get('/{blog}/comments/load-more', [BlogCommentController::class, 'loadMore'])->name('comments.loadMore');
        Route::delete('/comments/{comment}', [BlogCommentController::class, 'destroy'])->middleware('throttle:10,1')->name('comments.destroy');

        // Likes
        Route::post('/likes/toggle', [BlogLikeController::class, 'toggle'])->middleware('throttle:60,1')->name('likes.toggle');

        // Bookmarks
        Route::post('/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])->middleware('throttle:60,1')->name('bookmarks.toggle');
    });

    // Blog show route - MUST be after auth routes to avoid conflicts with /blogs/create
    Route::get('/{blog}', [BlogController::class, 'show'])->name('show');

    // Public route to load more replies for a specific comment (guest-accessible)
    Route::get('/{blog}/comments/{comment}/replies/load-more', [BlogCommentController::class, 'loadMoreReplies'])->name('comments.loadMoreReplies');

});

// Admin blog management routes
Route::middleware(['auth', 'verified', IsAdmin::class])->group(function () {
    Route::prefix('admin/blogs')->name('admin.blogs.')->group(function () {
        Route::get('/', [AdminBlogController::class, 'index'])->name('index');
        Route::get('/all-ids', [AdminBlogController::class, 'getAllPendingIds'])->name('all-ids');
        Route::post('/{blog}/approve', [AdminBlogController::class, 'approve'])->name('approve');
        Route::post('/approve/bulk', [AdminBlogController::class, 'approveBulk'])->name('approve.bulk');
        Route::post('/{blog}/reject', [AdminBlogController::class, 'reject'])->name('reject');
        Route::post('/reject/bulk', [AdminBlogController::class, 'rejectBulk'])->name('reject.bulk');
    });
});
