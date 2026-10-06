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
        'provider' => env('VEHICLE_API_PROVIDER', 'attestr'),
        'charge' => env('VEHICLE_API_CHARGE', 10.00),

        // Attestr's DPDP V3 transition. v3 is consent-driven: every lookup
        // must carry the "_id" of a consent registered beforehand. Set both
        // of the next two together - see docs/attestr-dpdpa-v3.md.
        'version' => env('VEHICLE_API_VERSION', 'v2'),

        // The request field that carries the consent id. Attestr mandates it
        // but has not published its name on any product page, so it is
        // configuration rather than a constant. Enabling v3 without it fails
        // loudly instead of sending a request Attestr will reject.
        'consent_field' => env('VEHICLE_API_CONSENT_FIELD', ''),

        // Who is emailed when Attestr refuses requests for a reason only the
        // operator can fix: low credit, bad credentials, an unwhitelisted IP,
        // a daily limit. Same recipient as the featured-plan expiry admin copy.
        'alert_email' => env('VEHICLE_API_ALERT_EMAIL', 'sachin60140@gmail.com'),

        'consent_register_url' => env(
            'VEHICLE_API_CONSENT_REGISTER_URL',
            'https://api.attestr.com/api/v3/public/consent/register'
        ),
    ],

    // Invincible Ocean, which powered e-challan and service-history lookups,
    // has shut down. Only the charge survives: Setting's historical-price
    // getters fall back to it, and without it they would silently report
    // 500 instead of 20 on the admin history screens.
    'service_history_api' => [
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
