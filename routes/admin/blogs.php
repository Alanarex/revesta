<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\AdminBlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Blog Management Routes
|--------------------------------------------------------------------------
|
| Routes for blog creation, editing, publishing, and admin moderation:
| - User blog CRUD operations (create, edit, update, delete, publish)
| - Admin blog approval and rejection workflows
| - Bulk operations for pending blog management
|
*/

Route::prefix('blogs')
    ->name('blogs.')
    ->group(function () {
        // User blog management
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/', [BlogController::class, 'store'])->middleware('throttle:10,1')->name('store');
        Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::put('/{blog}', [BlogController::class, 'update'])->middleware('throttle:20,1')->name('update');
        Route::post('/{blog}/publish', [BlogController::class, 'publish'])->middleware('throttle:10,1')->name('publish');
        Route::delete('/{blog}', [BlogController::class, 'destroy'])->middleware('throttle:10,1')->name('destroy');

        // Admin moderation and approval workflow
        Route::get('/', [AdminBlogController::class, 'index'])->name('index');
        Route::get('/all-ids', [AdminBlogController::class, 'getAllPendingIds'])->name('all-ids');
        Route::post('/{blog}/approve', [AdminBlogController::class, 'approve'])->name('approve');
        Route::post('/approve/bulk', [AdminBlogController::class, 'approveBulk'])->name('approve.bulk');
        Route::post('/{blog}/reject', [AdminBlogController::class, 'reject'])->name('reject');
        Route::post('/reject/bulk', [AdminBlogController::class, 'rejectBulk'])->name('reject.bulk');
    });


