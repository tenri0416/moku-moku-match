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

    // Google Analytics 4 
    'ga4' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID'),
    ],

    // AIプロバイダーの設定
    'google_ai' => [
        'api_key' => env('GOOGLE_AI_API_KEY'),
        'model' => env('GOOGLE_AI_MODEL', 'gemini-2.0-flash'),
    ],
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'model' => env('OPENROUTER_MODEL', 'deepseek/deepseek-r1-0528:free'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
    ],
    'ai' => [
        // 出力トークンを増やしたくないため、全AI共通で低めに固定
        'max_output_tokens' => env('AI_MAX_OUTPUT_TOKENS', 5000),
    ],
    /**
     * 各AIの1日あたりの推定上限
     * 実際のAPI残数ではなく、アプリ内の試行ログをもとに推定する
     */
    'provider_daily_limits' => [
        'google' => env('AI_GOOGLE_DAILY_LIMIT', 20),
        'openrouter' => env('AI_OPENROUTER_DAILY_LIMIT', 50),
        'groq' => env('AI_GROQ_DAILY_LIMIT', 100),
    ],

    // 管理者へのLINE通知設定
    'line' => [
        'access_token' => env('LINE_ACCESS_TOKEN'),
        'admin_to' => env('LINE_ADMIN_TO'),
    ],

    // google SSO
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => rtrim((string) env('APP_URL', 'http://localhost:8080'), '/') . '/auth/callback',
    ],
];
