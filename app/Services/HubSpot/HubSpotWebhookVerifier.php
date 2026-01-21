<?php

namespace App\Services\HubSpot;

use Illuminate\Http\Request;

class HubSpotWebhookVerifier
{
    private ?string $webhookSecret;

    public function __construct()
    {
        $this->webhookSecret = config('hubspot.webhook_secret');
    }

    /**
     * Verify HubSpot webhook signature
     */
    public function verify(Request $request): bool
    {
        // If no secret is configured, skip verification (for testing)
        if (! $this->webhookSecret) {
            return false;
        }

        $signature = $request->header('X-HubSpot-Signature')
            ?? $request->header('X-HubSpot-Signature-v3');

        if (! $signature) {
            return false;
        }

        $payload = $request->getContent();
        $requestUri = $request->getRequestUri();
        $method = $request->method();

        // HubSpot v3 signature format
        $sourceString = $method.$requestUri.$payload;
        $expectedSignature = hash_hmac('sha256', $sourceString, $this->webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }
}
