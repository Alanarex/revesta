<?php

/*
|--------------------------------------------------------------------------
| Web Route Loader
|--------------------------------------------------------------------------
|
| Loads the main web route groups. This file delegates route registration
| to smaller route files under the `routes/` directory: authentication,
| public pages and admin area routes.
|
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/public.php';
require __DIR__ . '/admin.php';
