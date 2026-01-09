<?php

return [
    'api_key' => env('BREVO_API_KEY'),
    'sender_email' => env('BREVO_SENDER_EMAIL', env('MAIL_FROM_ADDRESS')),
    'sender_name'  => env('BREVO_SENDER_NAME', env('MAIL_FROM_NAME', 'Sunshine Luxury Villas')),
];
