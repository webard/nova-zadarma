<?php

return [
    'enabled' => env('ZADARMA_ENABLED', true),

    'auth' => [
        'key' => env('ZADARMA_KEY'),
        'secret' => env('ZADARMA_SECRET'),
        'sip_login' => env('ZADARMA_SIP_LOGIN'),
    ],

    'models' => [
        'user' => [
            'class' => 'App\\Models\\User',
            'sip_field' => 'zadarma_sip',
            'name_field' => 'name',
            'phone_number_field' => 'phone_number',
        ],
    ],
    'nova_resources' => [
        'user' => [
            'class' => 'App\\Nova\\User',
        ],
        'phone_call' => [
            'register' => true,
        ],
    ],

    'webhook_controller' => \Webard\NovaZadarma\Http\Controllers\ZadarmaWebhookController::class,


    'webhook_log_channel' => env('ZADARMA_WEBHOOK_LOG_CHANNEL', 'null'),

    'webhook_verify_signature' => env('ZADARMA_WEBHOOK_VERIFY_SIGNATURE', true),

    // Supported any public disk or private disk with temporaryUrl() method
    // Public disk must have 'visibility' => 'public' in config/filesystems.php
    // Local disk is not supported because he doesn't have a temporaryUrl() method
    'recordings' => [
        'store' => true,
        'disk' => 'public',
        'path' => 'phone_call_recordings',
        'private_disk_ttl' => 60 * 10,
    ],

    'webhooks' => [
        'incoming_call_start' => '',
        'incoming_call_end' => '',

        'outgoing_call_start' => '',
        'outgoing_call_end' => '',

        'phone_call_record' => '',
    ],
];
