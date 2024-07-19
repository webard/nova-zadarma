<?php

use Illuminate\Support\Facades\Route;

Route::match(['POST'], '/pbx-call', [config('nova-zadarma.webhook_controller'), 'pbxCallWebhook']);
Route::match(['POST'], '/event', [config('nova-zadarma.webhook_controller'), 'eventWebhook']);
