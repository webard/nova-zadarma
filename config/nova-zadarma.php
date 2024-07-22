<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable or disable Zadarma integration
    |--------------------------------------------------------------------------
    */
    'enabled' => env('ZADARMA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Authorization keys
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'key' => env('ZADARMA_KEY'),
        'secret' => env('ZADARMA_SECRET'),
        'sip_login' => env('ZADARMA_SIP_LOGIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    */
    'models' => [
        'user' => [
            'class' => 'App\\Models\\User',
            'sip_field' => 'zadarma_sip',
            'name_field' => 'name',
            'phone_number_field' => 'phone_number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    */
    'nova_resources' => [
        'user' => [
            'class' => 'App\\Nova\\User',
        ],
        'phone_call' => [
            'register' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget settings
    |--------------------------------------------------------------------------
    */
    'widget' => [
        // square or rounded
        'shape' => 'rounded',
        // Supported languages: ru, en, es, fr, de, pl, ua
        'language' => 'en',
        'position' => [
            'left' => '20px',
            'bottom' => '20px',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Recordings
    |--------------------------------------------------------------------------
    |
    | Supported any public disk or private disk with temporaryUrl() method.
    | Public disk must have 'visibility' => 'public' in config/filesystems.php
    | Local disk is not supported because of lack a temporaryUrl() method.
    |
    */
    'recordings' => [
        // If false, link to zadarma storage will be used. Zadarma storage is available for specified time.
        'store' => true,
        'disk' => 'public',
        // Leave empty to store in the root of the disk
        'path' => 'phone_call_recordings',
        // When using private disk, you can specify the temporary URL TTL for audio player
        'private_disk_ttl' => 60 * 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    |
    | Here you can specify the controller that will handle Zadarma webhooks.
    | You can also disable signature verification for local development.
    |
    */
    'webhooks' => [
        'controller' => \Webard\NovaZadarma\Http\Controllers\ZadarmaWebhookController::class,
        // Keep it true for production
        'verify_signature' => env('ZADARMA_WEBHOOK_VERIFY_SIGNATURE', true),

        'classes' => [
            'incoming_call_start' => \Webard\NovaZadarma\Http\Controllers\Webhooks\IncomingCallStartController::class,
            'incoming_call_end' => \Webard\NovaZadarma\Http\Controllers\Webhooks\IncomingCallEndController::class,
            'outgoing_call_start' => \Webard\NovaZadarma\Http\Controllers\Webhooks\OutgoingCallStartController::class,
            'outgoing_call_end' => \Webard\NovaZadarma\Http\Controllers\Webhooks\OutgoingCallEndController::class,
            'phone_call_recording' => \Webard\NovaZadarma\Http\Controllers\Webhooks\RecordingController::class,
        ],
    ],
];
