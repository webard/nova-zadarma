<?php

return [
    'user_model' => 'App\\Models\\User',
    'sip_field' => 'zadarma_sip',
    'auth' => [
        'key' => env('ZADARMA_KEY'),
        'secret' => env('ZADARMA_SECRET'),
        'login_suffix' => env('ZADARMA_LOGIN_SUFFIX', 'UNKNOWN'),
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
