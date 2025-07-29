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
    'firebase' => [
        'api_key' => env('AIzaSyBxGFv8ZZUpBkS7InHxy6dc9ozM8uVk-Oc'),
        'auth_domain' => env('website-finance-mahasiswa.firebaseapp.com'),
        'project_id' => env('website-finance-mahasiswa'),
        'storage_bucket' => env('website-finance-mahasiswa.firebasestorage.app'),
        'messaging_sender_id' => env('802146642423'),
        'app_id' => env('1:802146642423:web:2a081831a4f8460700a6f2'),
        'vapid_key' => env('BGv4vERKz1eWa9Is4eQ2F-qWsy8Hhd8V2Zpwq2WLSYz5SKZ_9mdF1OKvxP3PUv4V9T_TX-t4r2PLvbyzVlQVzCU'),
],

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

];
