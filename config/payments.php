<?php

return [
    'bkash' => [
        'base_url' => env('BKASH_BASE_URL'),
        'username' => env('BKASH_USERNAME'),
        'password' => env('BKASH_PASSWORD'),
        'app_key' => env('BKASH_APP_KEY'),
        'app_secret' => env('BKASH_APP_SECRET'),
        'http' => [
            'timeout' => (int) env('BKASH_HTTP_TIMEOUT', 15),
        ],
    ],

    'sslcommerz' => [
        'store_id' => env('SSLC_STORE_ID'),
        'store_password' => env('SSLC_STORE_PASSWORD'),
        // true = sandbox, false = live
        'sandbox' => filter_var(env('SSLC_SANDBOX', true), FILTER_VALIDATE_BOOL),
        'http' => [
            'timeout' => (int) env('SSLC_HTTP_TIMEOUT', 10),
        ],
    ],
];
