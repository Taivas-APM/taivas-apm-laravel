<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    | Enable / Disable request data collection
    |--------------------------------------------------------------------------
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Secret
    | Set this to a random string. It's used to sign the JWT tokens which we
    | use for authentication. You can generate one in Laravel Tinker via:
    | Str::random(32)
    |--------------------------------------------------------------------------
    */
    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Tracking settings
    | Configure how requests are collected and stored
    |
    | Available persistence drivers:
    |  - sync
    |    Persist requests directly after the request ist returned to the client
    |
    |  - redis
    |    Push requests onto a redis list and persist them to the database
    |    asynchronously. Remember to call the taivas:persist command in your
    |    cronjob (app/Console/Kernel.php)
    |--------------------------------------------------------------------------
    */
    'tracking' => [
        'persistence_driver' => 'sync'
    ],

    /*
    |--------------------------------------------------------------------------
    | API
    | Configure the api which the taivas frontend (app.taivas.io) interacts
    | with.
    |--------------------------------------------------------------------------
    */
    'api' => [
        'prefix' => 'taivas',
        'auth' => [
            'lifetime' => 86400, // Specifies how long the JWT tokens are valid
            'guard' => null, // This defaults to auth.defaults.guard, you can set a specific guard here if you like
            'identifier' => 'email', // If your authentication guard uses a different column for identifying the user you can specify it here
        ],
    ],

];
