<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],

    'vehicle_api' => [
        'url' => env('VEHICLE_API_URL', 'https://api.attestr.com/api/v2/public/checkx/rc'),
        'key' => env('VEHICLE_API_KEY', ''),
        'auth_type' => env('VEHICLE_API_AUTH', 'bearer'),
        'provider' => env('VEHICLE_API_PROVIDER', 'attestr'),
        'charge' => env('VEHICLE_API_CHARGE', 10.00),
    ],

    'service_history_api' => [
        'url' => env('SERVICE_HISTORY_API_URL', 'https://api.invincibleocean.com/invincible/mahindra-service-history'),
        'secret_key' => env('SERVICE_HISTORY_SECRET_KEY', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL'),
        'client_id' => env('SERVICE_HISTORY_CLIENT_ID', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea'),
        'charge' => env('SERVICE_HISTORY_CHARGE', 20.00),
    ],

];
