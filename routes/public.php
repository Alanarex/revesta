<?php


/*
|--------------------------------------------------------------------------
| Public Route Loader
|--------------------------------------------------------------------------
|
| Includes all public-facing route definitions from the `routes/public/`
| directory. These routes typically serve the website pages and public
| resources that do not require admin privileges.
|
*/

foreach (glob(__DIR__ . '/public/*.php') as $filename) {
    require $filename;
}

