<?php

return [
    'ovipoint' => [
        'host' => env('OVIPOINT_DB_HOST', '127.0.0.1'),
        'port' => env('OVIPOINT_DB_PORT', '3306'),
        'database' => env('OVIPOINT_DB_DATABASE', 'oviprime_opdb'),
        'username' => env('OVIPOINT_DB_USERNAME'),
        'password' => env('OVIPOINT_DB_PASSWORD'),
        'socket' => env('OVIPOINT_DB_SOCKET', ''),
        'church_id' => (int) env('OVIPOINT_CHURCH_ID', 1),
    ],

    'booking' => [
        'max_party_size' => (int) env('TRANSPORT_MAX_PARTY_SIZE', 10),
    ],

    'notifications' => [
        'enabled' => (bool) env('TRANSPORT_NOTIFICATIONS_ENABLED', false),

        'sms' => [
            'enabled' => (bool) env('TRANSPORT_SMS_ENABLED', true),
            'recipients' => array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env('TRANSPORT_SMS_RECIPIENTS', ''))
            ))),
        ],

        'whatsapp' => [
            'enabled' => (bool) env('TRANSPORT_WHATSAPP_ENABLED', true),
            'recipients' => array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env('TRANSPORT_WHATSAPP_RECIPIENTS', ''))
            ))),
            'content_sid' => env('TWILIO_WHATSAPP_CONTENT_SID'),
        ],

        'twilio' => [
            'account_sid' => env('TWILIO_ACCOUNT_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'sms_from' => env('TWILIO_SMS_FROM'),
            'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
        ],
    ],
];
