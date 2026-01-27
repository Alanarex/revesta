<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Route Loader (v1)
|--------------------------------------------------------------------------
|
| Loads API route definitions under the `/api/v1` prefix. Individual API
| feature routes are stored in the `routes/api/` directory and are included
| here so they share the `api` middleware group and naming prefix.
|
*/

Route::prefix('v1')
    ->middleware('api')
    ->name('api.v1.')
    ->group(function () {

        foreach (glob(__DIR__ . '/api/v1/*.php') as $filename) {
            require $filename;
        }
    });
