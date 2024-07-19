<?php

use Illuminate\Support\Facades\Route;
use Webard\NovaZadarma\Http\Controllers\ZadarmaWebhookController;

Route::match(['GET', 'POST'], '/', config('nova-zadarma.webhook_controller', ZadarmaWebhookController::class));
