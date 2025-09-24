<?php

return [
    'paths' => ['api/*', 'inbox', '/api/inbox'],
    'allowed_methods' => ['*'], // or ['POST', 'OPTIONS']
    'allowed_origins' => [
        'https://www.sunshineluxuryvillas.co.uk',
        'https://sunshineluxuryvillas.co.uk',
    ],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Accept', 'Origin', 'Authorization'],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false, // keep false since the frontend isn't sending credentials
];
