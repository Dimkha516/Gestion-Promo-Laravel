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

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'), // Ajoutez cette ligne
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'private_key' => str_replace('\\n', "\n", env('FIREBASE_PRIVATE_KEY')),
        // 'credentials' => base_path(env('FIREBASE_CREDENTIALS')),
            // 'credentials' => ,
        'credentials' => json_decode(env('FIREBASE_CREDENTIALS', '{}'), true),
        'database_url' => env('FIREBASE_DATABASE_URL'),
    ],

];
