<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to admin management:
| - All admin routes are protected by auth, verified, and IsAdmin middleware
| - Child routes are defined in admin/ subdirectory
|
*/

Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'verified', IsAdmin::class])
    ->group(function () {
        require __DIR__ . '/admin/addresses.php';
    });
