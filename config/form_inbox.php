<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global notifications
    |--------------------------------------------------------------------------
    | These recipients are ALWAYS notified for every form submission.
    | They will be added as BCC in the email logic (recommended for reliability).
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
    | Keys MUST match the form_key sent by the frontend exactly.
    | Each form must define at least one recipient (to/cc/bcc),
    | otherwise no email will be sent.
    */

    // Property enquiry (from property details page)
    'property_details' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Investor Club sign-up
    'investor_club' => [
        'to'  => ['investorclub@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Request a callback form
    'request_callback' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Contact Us form
    'contact_us' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Sell With Us form
    'sell_with_us' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Affiliate enquiry
    'affiliate' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    // Newsletter subscribe
    'subscribe' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    | Used ONLY if a form_key is not defined above.
    | (Still includes _always recipients.)
    */
    '_default' => [
        'to'  => ['enquires@sunshineluxuryvillas.com'],
        'cc'  => [],
        'bcc' => [],
    ],

];
