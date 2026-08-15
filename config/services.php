<?php

return [
    'postmark' => ['key' => env('POSTMARK_API_KEY')],
    'resend' => ['key' => env('RESEND_API_KEY')],
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
    'ms_graph' => [
        'tenant_id' => env('MS_GRAPH_TENANT_ID'),
        'client_id' => env('MS_GRAPH_CLIENT_ID'),
        'client_secret' => env('MS_GRAPH_CLIENT_SECRET'),
        'sender' => env('MS_GRAPH_SENDER'),
    ],
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
    'google_search_console' => [
        'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
    ],

    'webex_interact' => [
        'token' => env('WEBEX_INTERACT_TOKEN'),
        'timeout' => (int) env('WEBEX_INTERACT_TIMEOUT', 15),
        'default_country_calling_code' => env('WEBEX_INTERACT_DEFAULT_COUNTRY_CALLING_CODE', '+44'),
        'sms' => [
            'enabled' => env('WEBEX_INTERACT_SMS_ENABLED', false),
            'from' => env('WEBEX_INTERACT_SMS_FROM'),
            'endpoint' => env('WEBEX_INTERACT_SMS_ENDPOINT', 'https://api.webexinteract.com/v1/sms'),
        ],
        'whatsapp' => [
            'enabled' => env('WEBEX_INTERACT_WHATSAPP_ENABLED', false),
            'profile_approved' => env('WEBEX_INTERACT_WHATSAPP_PROFILE_APPROVED', false),
            'profile_id' => env('WEBEX_INTERACT_WHATSAPP_PROFILE_ID'),
            'template_name' => env('WEBEX_INTERACT_WHATSAPP_TEMPLATE_NAME'),
        ],
        'transport' => [
            'notify_admin_sms' => env('TRANSPORT_NOTIFY_ADMIN_SMS', true),
            'notify_passenger_sms' => env('TRANSPORT_NOTIFY_PASSENGER_SMS', true),
            'admin_sms_recipients' => env('TRANSPORT_ADMIN_SMS_RECIPIENTS', ''),
            'notify_admin_whatsapp' => env('TRANSPORT_NOTIFY_ADMIN_WHATSAPP', false),
            'admin_whatsapp_recipients' => env('TRANSPORT_ADMIN_WHATSAPP_RECIPIENTS', ''),
        ],
    ],
];
