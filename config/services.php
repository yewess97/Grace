<?php

$login_social_providers = explode(',', env('LOGIN_SOCIAL_PROVIDERS'));

$socialite_defaults = array_combine(
    $login_social_providers,
    array_map(static fn($provider) => [
        'client_id'     => env(strtoupper($provider).'_CLIENT_ID'),
        'client_secret' => env(strtoupper($provider).'_CLIENT_SECRET'),
        'redirect'      => env("APP_URL")."/login/callback/{$provider}",
    ],
    $login_social_providers)
);

return array_merge([

    /*
    |--------------------------------------------------------------------------
    | Third Party Traits
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

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

], $socialite_defaults);
