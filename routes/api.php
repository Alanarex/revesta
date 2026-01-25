<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API endpoints under /api/v1 prefix.
| Child routes are defined in api/ subdirectory.
|
| Endpoints:
| - Authentication: /auth (register, login, logout, me)
| - Blogs: /blogs (list, show, like, comment)
|
*/

Route::prefix('api/v1')
    ->name('api.v1.')
    ->group(function () {
        foreach (glob(__DIR__ . '/api/*.php') as $filename) {
            require $filename;
        }
    });
