<?php

/*
|--------------------------------------------------------------------------
| Auth Routes Loader
|--------------------------------------------------------------------------
|
| Registers the authentication-related web routes. Splits guest and
| authenticated route groups into separate files under `routes/auth/` for
| clarity and maintainability.
|
*/

Route::prefix('auth')->group(function () {
    require __DIR__.'/auth/guest.php';
    require __DIR__.'/auth/authenticated.php';
});