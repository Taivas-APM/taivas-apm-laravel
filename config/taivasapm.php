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

    'api' => [
        'prefix' => 'taivas',
    ],

    'auth' => [
        'lifetime' => 86400, // Specifies how long the JWT tokens are valid
        'guard' => null, // This defaults to auth.defaults.guard, you can set a specific guard here if you like
        'identifier' => 'email', // If your authentication guard uses a different column for identifying the user you can specify it here
    ],
];
