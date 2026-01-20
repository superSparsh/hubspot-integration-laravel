<?php

use App\Http\Controllers\HubSpotAuthController;
use App\Http\Controllers\HubSpotWebhookController;
use Illuminate\Support\Facades\Route;

// HubSpot OAuth Routes
Route::prefix('integrations/hubspot')->group(function () {
    Route::get('connect', [HubSpotAuthController::class, 'connect'])->name('hubspot.connect');
    Route::get('callback', [HubSpotAuthController::class, 'callback'])->name('hubspot.callback');
});

// HubSpot Webhook Route
Route::post('webhooks/hubspot', [HubSpotWebhookController::class, 'handle'])->name('hubspot.webhook');

// Dashboard and Triggers (add authentication middleware as needed)
Route::get('/', function () {
    return view('dashboard');
});
