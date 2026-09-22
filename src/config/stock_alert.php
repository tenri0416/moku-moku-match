<?php

return [
    'internal_token' => env('STOCK_ALERT_INTERNAL_TOKEN'),

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),

        // Stock Alert専用
        // 既存のGROQ_MODELには影響させない
        'model' => 'openai/gpt-oss-20b',

        'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
        'timeout' => 30,
    ],

    'line' => [
        'channel_access_token' => env('LINE_ACCESS_TOKEN'),
        'user_id' => env('LINE_ADMIN_TO'),

        'endpoint' => 'https://api.line.me/v2/bot/message/push',
    ],

    'news' => [
        'max_items' => 20,
        'initial_items' => 5,
        'lookback_days' => 7,
        'timeout' => 15,
    ],

    'alert' => [
        'min_confidence' => 60,
        'line_text_limit' => 4500,
    ],
];
