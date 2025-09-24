<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */
    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */
    'mailers' => [
        // Default Gmail (slv.noreply.app@gmail.com)
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'localhost'),
            'port' => env('MAIL_PORT', 1025),
            'encryption' => env('MAIL_ENCRYPTION', null),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        // Investor Club mailer
        'investorclub' => [
            'transport' => env('INVESTOR_MAIL_MAILER', 'smtp'),
            'host' => env('INVESTOR_MAIL_HOST'),
            'port' => env('INVESTOR_MAIL_PORT'),
            'encryption' => env('INVESTOR_MAIL_ENCRYPTION'),
            'username' => env('INVESTOR_MAIL_USERNAME'),
            'password' => env('INVESTOR_MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        // Enquiries mailer
        'enquires' => [
            'transport' => env('ENQUIRES_MAIL_MAILER', 'smtp'),
            'host' => env('ENQUIRES_MAIL_HOST'),
            'port' => env('ENQUIRES_MAIL_PORT'),
            'encryption' => env('ENQUIRES_MAIL_ENCRYPTION'),
            'username' => env('ENQUIRES_MAIL_USERNAME'),
            'password' => env('ENQUIRES_MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'slv.noreply.app@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'Sunshine Luxury Villas'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    */
    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
