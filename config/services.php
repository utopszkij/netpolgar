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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id' => env('Facebook_app_id',''),
        'client_secret' => env('Facebook_secret',''),
        'redirect' => env('APP_URL').'/auth/facebook/callback',
    ],

    'google' => [
        'client_id' => env('Google_app_id',''),
        'client_secret' => env('Google_secret',''),
        'redirect' => env('APP_URL').'/auth/google/callback',
    ],

    'github' => [
        'client_id' => env('Github_app_id',''),
        'client_secret' => env('Github_secret',''),
        'redirect' => env('APP_URL').'/auth/github/callback',
    ],

];
