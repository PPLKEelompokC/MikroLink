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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ai_allocation' => [
        'driver' => env('AI_ALLOCATION_DRIVER', 'llamacpp'),
        'llamacpp' => [
            'base_url' => env('AI_LLAMACPP_BASE_URL', 'http://127.0.0.1:8080'),
            'model' => env('AI_LLAMACPP_MODEL', 'Qwen3.5-9B-UD-Q2_K_XL.gguf'),
        ],
        'openrouter' => [
            'base_url' => env('AI_OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            'api_key' => env('AI_OPENROUTER_API_KEY'),
            // Primary model. When rate-limited, OpenRouter automatically tries fallback_models below.
            'model' => env('AI_OPENROUTER_MODEL', 'google/gemini-2.5-flash'),
            // Fallback models tried in order when the primary is rate-limited.
            // NOTE: OpenRouter's `models` array allows max 3 items total (primary + 2 fallbacks).
            // Gemini fallbacks use your BYOK Google AI key (Google AI Studio provider).
            // Gemma is open-source and hosted on third-party providers, so BYOK won't apply to it.
            'fallback_models' => [
                'google/gemini-3-flash-preview',      // Gemini fallback (BYOK applies, uses your quota)
                'google/gemma-3-27b-it:free',         // Gemma last resort (completely free on OpenRouter)
            ],
            // Provider routing: OpenRouter will try this provider first (BYOK), then fall
            // back to any other available provider for the model if it fails.
            // Set to null/empty to let OpenRouter load-balance freely across all providers.
            'prefer_provider' => env('AI_OPENROUTER_PREFER_PROVIDER', 'Google AI Studio'),
        ],
        'google' => [
            'base_url' => env('AI_GOOGLE_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'api_key' => env('AI_GOOGLE_API_KEY'),
            'model' => env('AI_GOOGLE_MODEL', 'gemini-3-flash-preview'),
            // Backup models used when the primary model fails (e.g., rate limits)
            'fallback_models' => [
                'gemini-3-1-flash-lite-preview',
                'gemini-2-5-flash',
                'gemini-2-5-flash-lite',
            ],
        ],
        'timeout' => (int) env('AI_ALLOCATION_TIMEOUT', 120),
        'operational_reserve_ratio' => (float) env('AI_ALLOCATION_RESERVE_RATIO', 0.20),
    ],

    'ocr_space' => [
        'key' => env('OCR_SPACE_API_KEY'),
    ],

];
