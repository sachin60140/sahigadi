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

    'phonepe' => [
        'merchant_id' => env('PHONEPE_CLIENT_ID', env('PHONEPE_MERCHANT_ID')),
        'salt_key' => env('PHONEPE_CLIENT_SECRET', env('PHONEPE_SALT_KEY')),
        'salt_index' => env('PHONEPE_CLIENT_VERSION', env('PHONEPE_SALT_INDEX', '1')),
        'env' => env('PHONEPE_ENV', 'UAT'),
        'checkout_url' => env('PHONEPE_CHECKOUT_URL'),
        'webhook_user' => env('PHONEPE_WEBHOOK_USER'),
        'webhook_pass' => env('PHONEPE_WEBHOOK_PASS'),
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
        'secret_key' => env('SERVICE_HISTORY_SECRET_KEY'),
        'client_id' => env('SERVICE_HISTORY_CLIENT_ID'),
        'charge' => env('SERVICE_HISTORY_CHARGE', 20.00),
    ],

    'smartping' => [
        'api_url' => env('SMARTPING_API_URL', 'https://pgapi.sparc.smartping.io/fe/api/v1/send'),
        'username' => env('SMARTPING_USERNAME'),
        'password' => env('SMARTPING_PASSWORD'),
        'sender_id' => env('SMARTPING_SENDER_ID', 'INSARS'),
        'dlt_content_id' => env('SMARTPING_DLT_CONTENT_ID', '1707177677498830200'),
        'dlt_principal_id' => env('SMARTPING_DLT_PRINCIPAL_ID', '1701166126846262605'),
    ],

];
