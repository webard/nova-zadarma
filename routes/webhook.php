<?php

use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST'], '/webhook/pbx-call', [config('nova-zadarma.webhook_controller'), 'pbxCallWebhook']);

Route::match(['GET', 'POST'], '/webhook/event', [config('nova-zadarma.webhook_controller'), 'eventWebhook']);
