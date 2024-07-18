<?php

return [
    'enabled' => env('ZADARMA_ENABLED', true),
    'models' => [
        'user' => 'App\\Models\\User',
    ],
    // @deprecated
    'user_model' => 'App\\Models\\User',
    'sip_field' => 'zadarma_sip',
    'auth' => [
        'key' => env('ZADARMA_KEY'),
        'secret' => env('ZADARMA_SECRET'),
        'sip_login' => env('ZADARMA_SIP_LOGIN'),
    ],

    'webhook_controller' => \Webard\NovaZadarma\Http\Controllers\ZadarmaWebhookController::class,

    'phone_number_info_handler' => '',

    'webhook_log_channel' => env('ZADARMA_WEBHOOK_LOG_CHANNEL', 'null'),

    'webhooks' => [
        'incoming_call_start' => '',
        'incoming_call_end' => '',

        'outgoing_call_start' => '',
        'outgoing_call_end' => '',

        'phone_call_record' => '',
    ],
];
