<?php

use Illuminate\Support\Facades\Route;
use Webard\NovaZadarma\Http\Controllers\ZadarmaWebhookForwardController;

Route::match(['GET', 'POST'], '/', config('nova-zadarma.webhook_controller', ZadarmaWebhookForwardController::class));
