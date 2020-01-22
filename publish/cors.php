<?php

declare(strict_types=1);

return [
    'allow_credentials' => false,

    'allow_origins' => [
        '*',
    ],

    'allow_methods' => [
        'POST',
        'GET',
        'OPTIONS',
        'PUT',
        'PATCH',
        'DELETE',
    ],

    'allow_headers' => [
        'Content-Type',
        'X-Auth-Token',
        'Origin',
        'Authorization',
    ],

    'expose_headers' => [
        'Cache-Control',
        'Content-Language',
        'Content-Type',
        'Expires',
        'Last-Modified',
        'Pragma',
    ],

    'forbidden_response' => [
        'message' => 'Forbidden (cors).',
        'status'  => 403,
    ],

    'max_age' => 60 * 60 * 24
];
