<?php

return [
    'user' => [
        'model' => \App\Models\User::class,
    ],
    'middleware' => [
        'auth' => 'auth',
    ],

    'api_url' => env('PI_API_URL', 'https://api.minepi.com'),
    'api_version' => env('PI_API_VERSION', 'v2'),
    'api_key' => env('PI_API_KEY', ''),
];
