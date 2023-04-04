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

    'amocrm' => [ 
        'key' => env('AMOCRM_ACCESS_KEY_ID'),
        'base_domain' => env('AMOCRM_BASE_DOMAIN'),
        'secret' => env('AMOCRM_SECRET_ACCESS_KEY'),
        'redirect_uri' => env('AMOCRM_REDIRECT_URI', env('APP_URL')),
        'authorization_code' => env('AMOCRM_AUTHORIZATION_CODE'),
    ],

];
