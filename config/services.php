<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'payment_gateway' => [
        'provider' => 'doku',
        'client_id' => env('DOKU_CLIENT_ID', env('PAYMENT_GATEWAY_KEY')),
        'secret_key' => env('DOKU_SECRET_KEY', env('PAYMENT_GATEWAY_SECRET')),
        'base_url' => env('DOKU_BASE_URL', env('PAYMENT_GATEWAY_URL', 'https://api-sandbox.doku.com')),
        'ca_bundle' => env('DOKU_CA_BUNDLE', 'C:/laragon/etc/ssl/cacert.pem'),
        'notification_url' => env('DOKU_NOTIFICATION_URL', rtrim(env('APP_URL', 'http://localhost'), '/') . '/api/v1/payments/webhook/doku'),
        'return_url' => env('DOKU_RETURN_URL', rtrim(env('APP_URL', 'http://localhost'), '/') . '/payment/return'),
        'callback_url' => env('DOKU_CALLBACK_URL', rtrim(env('APP_URL', 'http://localhost'), '/') . '/payment/finish'),
        'currency' => env('PAYMENT_CURRENCY', 'IDR'),
        'expiry_minutes' => env('PAYMENT_EXPIRY_MINUTES', 60),
        'sandbox' => env('PAYMENT_SANDBOX', true),
        // legacy aliases
        'url' => env('DOKU_BASE_URL', env('PAYMENT_GATEWAY_URL')),
        'key' => env('DOKU_CLIENT_ID', env('PAYMENT_GATEWAY_KEY')),
        'secret' => env('DOKU_SECRET_KEY', env('PAYMENT_GATEWAY_SECRET')),
        'webhook_secret' => env('DOKU_SECRET_KEY', env('PAYMENT_WEBHOOK_SECRET')),
    ],

    'tracking' => [
        'provider' => 'biteship',
        'api_key' => env('BITESHIP_API_KEY', env('TRACKING_API_KEY')),
        'base_url' => env('BITESHIP_BASE_URL', env('TRACKING_API_URL', 'https://api.biteship.com')),
        'ca_bundle' => env('BITESHIP_CA_BUNDLE', 'C:/laragon/etc/ssl/cacert.pem'),
        'webhook_header' => env('BITESHIP_WEBHOOK_HEADER', 'X-Biteship-Signature'),
        'webhook_secret' => env('BITESHIP_WEBHOOK_SECRET', env('TRACKING_WEBHOOK_SECRET')),
        'origin' => [
            'contact_name' => env('BITESHIP_ORIGIN_CONTACT_NAME'),
            'contact_phone' => env('BITESHIP_ORIGIN_CONTACT_PHONE'),
            'contact_email' => env('BITESHIP_ORIGIN_CONTACT_EMAIL'),
            'organization' => env('BITESHIP_ORIGIN_ORGANIZATION'),
            'address' => env('BITESHIP_ORIGIN_ADDRESS'),
            'postal_code' => env('BITESHIP_ORIGIN_POSTAL_CODE'),
            'latitude' => env('BITESHIP_ORIGIN_LATITUDE'),
            'longitude' => env('BITESHIP_ORIGIN_LONGITUDE'),
        ],
        'default_weight' => (int) env('BITESHIP_DEFAULT_WEIGHT', 200),
        'default_length' => (int) env('BITESHIP_DEFAULT_LENGTH', 10),
        'default_width' => (int) env('BITESHIP_DEFAULT_WIDTH', 10),
        'default_height' => (int) env('BITESHIP_DEFAULT_HEIGHT', 10),
        'sandbox' => env('TRACKING_SANDBOX', true),
        // legacy aliases
        'url' => env('BITESHIP_BASE_URL', env('TRACKING_API_URL')),
        'key' => env('BITESHIP_API_KEY', env('TRACKING_API_KEY')),
        'secret' => env('BITESHIP_API_KEY', env('TRACKING_API_SECRET')),
    ],

];
