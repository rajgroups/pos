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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'socket' => [
        'url' => env('SOCKET_SERVER_URL', 'http://127.0.0.1:9502'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'google_cloud' => [
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        'billing_account_id' => env('GOOGLE_CLOUD_BILLING_ACCOUNT_ID'),
        'credentials' => env('GOOGLE_CLOUD_CREDENTIALS'),
        'bigquery' => [
            'project_id' => env('GOOGLE_CLOUD_BIGQUERY_PROJECT_ID'),
            'dataset' => env('GOOGLE_CLOUD_BIGQUERY_DATASET'),
            'billing_table' => env('GOOGLE_CLOUD_BIGQUERY_BILLING_TABLE'),
        ],
        'alerts' => [
            'daily_inr' => env('GOOGLE_MAPS_DAILY_ALERT_INR', 500),
            'monthly_inr' => env('GOOGLE_MAPS_MONTHLY_ALERT_INR', 10000),
        ],
    ],

    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
        'service_account_file' => env('FCM_SERVICE_ACCOUNT_FILE', storage_path('app/firebase/firebase-service-account.json')),
    ],

    'sms_gateway' => [
        'registration_token' => env('SMS_GATEWAY_REGISTRATION_TOKEN'),
        'timeout_minutes' => env('SMS_GATEWAY_TIMEOUT', 10),
        'debug_return_otp' => env('SMS_GATEWAY_DEBUG_RETURN_OTP', true),
    ],

];
