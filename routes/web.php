<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HubSpotAuthController;
use App\Http\Controllers\HubSpotWebhookController;
use App\Http\Controllers\TriggerController;
use App\Http\Controllers\WapappAuthController;
use Illuminate\Support\Facades\Route;

// ============================================
// WAPAPP Authentication Routes
// ============================================
Route::get('/', [WapappAuthController::class, 'showLoginForm'])->name('wapapp.login');
Route::post('/login', [WapappAuthController::class, 'login'])->name('wapapp.login.post');
Route::post('/logout', [WapappAuthController::class, 'logout'])->name('wapapp.logout');

// ============================================
// HubSpot OAuth Routes (Public)
// ============================================
Route::prefix('integrations/hubspot')->group(function () {
    Route::get('connect', [HubSpotAuthController::class, 'connect'])->name('hubspot.connect');
    Route::get('callback', [HubSpotAuthController::class, 'callback'])->name('hubspot.callback');
});

// ============================================
// HubSpot Webhook Route (Public)
// ============================================
Route::post('webhooks/hubspot', [HubSpotWebhookController::class, 'handle'])->name('hubspot.webhook');

// ============================================
// Protected Routes (Require WAPAPP Authentication)
// ============================================
Route::middleware('wapapp.auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/api-token', [DashboardController::class, 'updateApiToken'])->name('api-token.update');
    
    // Triggers CRUD
    Route::get('/triggers/create', [TriggerController::class, 'create'])->name('triggers.create');
    Route::post('/triggers', [TriggerController::class, 'store'])->name('triggers.store');
    Route::get('/triggers/{uuid}/edit', [TriggerController::class, 'edit'])->name('triggers.edit');
    Route::put('/triggers/{uuid}', [TriggerController::class, 'update'])->name('triggers.update');
    Route::delete('/triggers/{id}', [TriggerController::class, 'destroy'])->name('triggers.destroy');
    Route::post('/triggers/{id}/test', [TriggerController::class, 'test'])->name('triggers.test');
    
    // API Endpoints
    Route::get('/api/payload-fields', [TriggerController::class, 'getPayloadFields'])->name('api.payload-fields');
});
