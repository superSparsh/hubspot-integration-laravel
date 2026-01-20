<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HubSpotAuthController;
use App\Http\Controllers\HubSpotWebhookController;
use App\Http\Controllers\TriggerController;
use App\Http\Controllers\WapappAuthController;
use Illuminate\Support\Facades\Route;

// ============================================
// Landing Page - Connect HubSpot First
// ============================================
Route::get('/', function () {
    // If already authenticated with both HubSpot and WAPAPP, go to dashboard
    if (session('hubspot_portal_id') && session('wapapp_user')) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// ============================================
// HubSpot OAuth Routes (Step 1)
// ============================================
Route::prefix('integrations/hubspot')->group(function () {
    Route::get('connect', [HubSpotAuthController::class, 'connect'])->name('hubspot.connect');
    Route::get('callback', [HubSpotAuthController::class, 'callback'])->name('hubspot.callback');
});

// ============================================
// WAPAPP Authentication (Step 2 - After HubSpot)
// ============================================
Route::get('/wapapp/login', [WapappAuthController::class, 'showLoginForm'])->name('wapapp.login');
Route::post('/wapapp/login', [WapappAuthController::class, 'login'])->name('wapapp.login.post');
Route::post('/wapapp/logout', [WapappAuthController::class, 'logout'])->name('wapapp.logout');

// ============================================
// HubSpot Webhook Route (Public)
// ============================================
Route::post('webhooks/hubspot', [HubSpotWebhookController::class, 'handle'])->name('hubspot.webhook');

// ============================================
// Protected Routes (Require both HubSpot + WAPAPP)
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
