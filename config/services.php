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

    'biznet_face' => [
        'base_url' => env('BIZNET_FACE_BASE_URL', 'https://fr.neoapi.id/risetai/face-api'),
        'access_token' => env('BIZNET_FACE_ACCESS_TOKEN'),
        'default_facegallery_id' => env('BIZNET_FACE_GALLERY_ID', 'jakakuasa.production'),
        'similarity_threshold' => env('BIZNET_FACE_SIMILARITY_THRESHOLD', 0.75),
        'timeout' => env('BIZNET_FACE_TIMEOUT', 30),
    ],


];
