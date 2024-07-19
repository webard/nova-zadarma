<?php

return [
    'enabled' => env('ZADARMA_ENABLED', true),
    'register_resource' => true,
    'models' => [
        'user' => 'App\\Models\\User',
    ],
    'resources' => [
        'user' => 'App\\Nova\\User',
    ],
    // @deprecated
    'user_model' => 'App\\Models\\User',
    'sip_field' => 'zadarma_sip',
    'auth' => [
        'key' => env('ZADARMA_KEY'),
        'secret' => env('ZADARMA_SECRET'),
        'sip_login' => env('ZADARMA_SIP_LOGIN'),
    ],

    'webhook_controller' => \Webard\NovaZadarma\Http\Controllers\ZadarmaWebhookForwardController::class,

    'phone_number_info_handler' => '',

    'webhook_log_channel' => env('ZADARMA_WEBHOOK_LOG_CHANNEL', 'null'),

    'webhook_verify_signature' => env('ZADARMA_WEBHOOK_VERIFY_SIGNATURE', true),

    'webhooks' => [
        'incoming_call_start' => '',
        'incoming_call_end' => '',

        'outgoing_call_start' => '',
        'outgoing_call_end' => '',

        'phone_call_record' => '',
    ],
];
