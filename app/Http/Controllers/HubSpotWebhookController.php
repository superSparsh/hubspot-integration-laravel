<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HubSpotWebhookController extends Controller
{
    /**
     * Handle incoming HubSpot webhook - simplified for debugging
     */
    public function handle(Request $request)
    {
        // Log everything for debugging
        logger()->info('=== WEBHOOK RECEIVED ===');
        logger()->info('Method: ' . $request->method());
        logger()->info('URL: ' . $request->fullUrl());
        logger()->info('Headers: ' . json_encode($request->headers->all()));
        logger()->info('Body: ' . $request->getContent());
        
        return response()->json([
            'status' => 'ok',
            'message' => 'Webhook received successfully',
            'timestamp' => now()->toDateTimeString(),
        ], 200);
    }
}
