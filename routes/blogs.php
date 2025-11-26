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
| - Admin blog moderation
|
*/

// Public blog routes (guests and authenticated users)
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');

// Authenticated blog routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Bookmarks - named without the 'blogs.' prefix so views can use route('bookmarks.index')
    // Only provide listing and rely on toggle endpoint for add/remove. No delete route needed.
    Route::get('/blogs/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    // Blog management routes (specific routes BEFORE wildcard)
    Route::prefix('blogs')->name('blogs.')->group(function () {
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/', [BlogController::class, 'store'])->middleware('throttle:10,1')->name('store');
        Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::put('/{blog}', [BlogController::class, 'update'])->middleware('throttle:20,1')->name('update');
        Route::delete('/{blog}', [BlogController::class, 'destroy'])->middleware('throttle:10,1')->name('destroy');

        // Comments
        Route::post('/{blog}/comments', [BlogCommentController::class, 'store'])->middleware('throttle:20,1')->name('comments.store');
        // Note: loading more top-level comments stays here (auth users), but replies loading should be public
        Route::get('/{blog}/comments/load-more', [BlogCommentController::class, 'loadMore'])->name('comments.loadMore');
        Route::delete('/comments/{comment}', [BlogCommentController::class, 'destroy'])->middleware('throttle:10,1')->name('comments.destroy');

        // Likes
        Route::post('/likes/toggle', [BlogLikeController::class, 'toggle'])->middleware('throttle:60,1')->name('likes.toggle');

        // Bookmarks
        Route::post('/bookmarks/toggle', [BlogBookmarkController::class, 'toggle'])->middleware('throttle:60,1')->name('bookmarks.toggle');
    // Bookmarks toggle endpoint remains here; the full listing routes are named without the 'blogs.' prefix above.
    });
});

// Blog show route - MUST be after auth routes to avoid conflicts with /blogs/create
Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');

// Public route to load more replies for a specific comment (guest-accessible)
Route::get('/blogs/{blog}/comments/{comment}/replies/load-more', [BlogCommentController::class, 'loadMoreReplies'])->name('comments.loadMoreReplies');

// Admin blog moderation routes
Route::middleware(['auth', 'verified', IsAdmin::class])->group(function () {
    Route::prefix('admin/blogs')->name('admin.blogs.')->group(function () {
        Route::get('/', [AdminBlogController::class, 'index'])->name('index');
        Route::get('/all-ids', [AdminBlogController::class, 'getAllPendingIds'])->name('all-ids');
        Route::post('/bulk-action', [AdminBlogController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{blog}', [AdminBlogController::class, 'show'])->name('show');
        Route::post('/{blog}/approve', [AdminBlogController::class, 'approve'])->name('approve');
        Route::post('/{blog}/reject', [AdminBlogController::class, 'reject'])->name('reject');
    });
});
