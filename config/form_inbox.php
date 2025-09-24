<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Global notifications
    |--------------------------------------------------------------------------
    | Everyone here is ALWAYS notified for ANY form (we’ll add them as CC).
    */
    '_always' => [
        'paul@sunshineluxuryvillas.com',
        'jake@sunshineluxuryvillas.com',
        'haroldvan.boomering@outlook.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-form recipients
    |--------------------------------------------------------------------------
    | Use the form_key your frontend posts with.
    | - Investor club signups  -> investorclub@...
    | - Callback/Contact/Property enquiries -> enquires@...
    */
    'property_details' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],  // include property reference in subject/body
        'cc'  => [],
        'bcc' => [],
    ],

    'investor_club' => [
        'to'  => ['investorclub@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // “Request a callback” form (make sure FE posts form_key = request_callback)
    'request_callback' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],  // include current page URL in body
        'cc'  => [],
        'bcc' => [],
    ],

    'contact_us' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Not specified—fall back to _default (below) unless you want to hard-route these too:
    'sell_with_us' => [],
    'affiliate'    => [],

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    | If a form_key isn’t listed above, we’ll send here (and still CC _always).
    */
    '_default' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],
];
