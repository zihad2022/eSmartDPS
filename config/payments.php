<?php

return [
    'sslcommerz' => [
        'store_id' => env('SSLC_STORE_ID', 'softc6610e80407051'),
        'store_password' => env('SSLC_STORE_PASSWORD', 'softc6610e80407051@ssl'),
        // true = sandbox, false = live
        'sandbox' => env('SSLC_SANDBOX', true),
        // Optional timeouts and options for HTTP client
        'http' => [
            'timeout' => env('SSLC_HTTP_TIMEOUT', 10),
        ],
    ],
];
