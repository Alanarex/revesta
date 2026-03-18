<?php

use App\Http\Controllers\PublicBlogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('blogs')
    ->name('blogs.')
    ->controller(PublicBlogController::class)
    ->group(function () {
        Route::get('/{blog}', 'show')->name('show');
    });
