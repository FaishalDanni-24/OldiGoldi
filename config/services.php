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
        'url' => env('PAYMENT_GATEWAY_URL'),
        'key' => env('PAYMENT_GATEWAY_KEY'),
        'secret' => env('PAYMENT_GATEWAY_SECRET'),
        'webhook_secret' => env('PAYMENT_WEBHOOK_SECRET'),
        'callback_url' => env('PAYMENT_CALLBACK_URL'),
        'return_url' => env('PAYMENT_RETURN_URL'),
        'currency' => env('PAYMENT_CURRENCY', 'IDR'),
        'expiry_minutes' => env('PAYMENT_EXPIRY_MINUTES', 60),
        'sandbox' => env('PAYMENT_SANDBOX', true),
    ],

    'tracking' => [
        'url' => env('TRACKING_API_URL'),
        'key' => env('TRACKING_API_KEY'),
        'secret' => env('TRACKING_API_SECRET'),
        'webhook_secret' => env('TRACKING_WEBHOOK_SECRET'),
        'sandbox' => env('TRACKING_SANDBOX', true),
    ],

];
