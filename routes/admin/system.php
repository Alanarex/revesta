<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('system')
    ->name('system.')
    ->group(function () {
        Route::get('/phpmyadmin', function (Request $request) {
            $url = (string) config('services.phpmyadmin.url');

            abort_if($url === '', 404, 'phpMyAdmin URL is not configured.');

            if (app()->environment('production')) {
                $allowedIps = (array) config('services.phpmyadmin.allowed_ips', []);

                if (! empty($allowedIps)) {
                    abort_unless(in_array($request->ip(), $allowedIps, true), 403);
                }
            }

            return redirect()->away($url);
        })->name('phpmyadmin');
    });
