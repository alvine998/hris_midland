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

    'whatsapp' => [
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    ],

    'bigmodel' => [
        'api_key' => env('BIGMODEL_API_KEY'),
        'model' => env('BIGMODEL_MODEL', 'glm-5.1'),
        'temperature' => env('BIGMODEL_TEMPERATURE', 0.2),
        'max_tokens' => env('BIGMODEL_MAX_TOKENS', 1024),
    ],

    'whatsapp_ai' => [
        'session_ttl_minutes' => env('WHATSAPP_AI_SESSION_TTL', 30),
        'max_history_messages' => env('WHATSAPP_AI_MAX_HISTORY', 10),
        'rate_limit_per_minute' => env('WHATSAPP_AI_RATE_LIMIT', 10),
        'auth_flow_timeout_minutes' => env('WHATSAPP_AI_AUTH_TIMEOUT', 5),
    ],

];
