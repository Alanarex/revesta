<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Basic information
    |--------------------------------------------------------------------------
    */
    'title' => env('APP_NAME', 'Revesta API'),
    'description' => 'API documentation for the Revesta application.',
    'base_url' => env('APP_URL', 'http://revesta.local'),

    /*
    |--------------------------------------------------------------------------
    | Routes to include in the documentation
    |--------------------------------------------------------------------------
    */
    'routes' => [
        // Only document API v1 routes to avoid scanning web/debug routes
        [
            'include' => [
                'api/v1/*',
            ],
            'exclude' => [
                '_debugbar*',
                'oauth*',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication (used by "Try it out")
    |--------------------------------------------------------------------------
    |
    | Enable auth UI and configure where to place the token. For this
    | project we use Passport bearer tokens in the Authorization header.
    */
    'auth' => [
        'enabled' => true,
        // where to place the authentication token when making Try-it requests
        'in' => 'header', // header|query|cookie
        'name' => 'Authorization',
        // a default value shown in the input (dev convenience)
        'use_value' => env('SCRIBE_AUTH_VALUE', 'Bearer {token}'),
        'placeholder' => 'Bearer {token}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Try-it-out options
    |--------------------------------------------------------------------------
    */
    'try_it_out' => [
        'enabled' => true,
        // whether to show the authentication UI
        'show_auth' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel route settings for the documentation UI
    |--------------------------------------------------------------------------
    |
    | Scribe registers a small set of routes (the HTML UI and the
    | Postman/OpenAPI endpoints). We protect those routes using the
    | `auth` middleware and the application's `IsAdmin` middleware so
    | only authenticated admin users can access the docs.
    |
    */
    'laravel' => [
        'docs_url' => env('SCRIBE_DOCS_URL', '/docs'),
        'middleware' => [
            'web',
            'auth',
            \App\Http\Middleware\IsAdmin::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Misc
    |--------------------------------------------------------------------------
    */
    'examples' => [
        'requests' => ['bash', 'javascript'],
    ],
];
