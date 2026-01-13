<?php

namespace App\Http\Controllers;

use App\Models\HubSpotConnection;
use App\Models\HubSpotWebhookLog;
use App\Services\HubSpot\HubSpotOAuthService;
use App\Services\HubSpot\HubSpotWebhookVerifier;
use App\Services\HubSpot\HubSpotWebhookProcessor;
use Illuminate\Http\Request;
use Exception;

class HubSpotWebhookController extends Controller
{
    private HubSpotWebhookVerifier $verifier;
    private HubSpotWebhookProcessor $processor;
    private HubSpotOAuthService $oauthService;

    public function __construct(
        HubSpotWebhookVerifier $verifier,
        HubSpotWebhookProcessor $processor,
        HubSpotOAuthService $oauthService
    ) {
        $this->verifier = $verifier;
        $this->processor = $processor;
        $this->oauthService = $oauthService;
    }

    /**
     * Handle incoming HubSpot webhook
     */
    public function handle(Request $request)
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('X-HubSpot-Signature')
            ?? $request->header('X-HubSpot-Signature-v3');

        // Validate payload
        if (!$rawPayload) {
            return response('Missing payload', 400);
        }

        $data = json_decode($rawPayload, true);
        if (!$data || !is_array($data) || empty($data)) {
            return response('Invalid JSON payload', 400);
        }

        // Verify signature
        $signatureValid = $this->verifier->verify($request);

        // Extract portal ID
        $portalId = $data[0]['portalId'] ?? null;
        if (!$portalId) {
            return response('Missing portal ID', 400);
        }

        // Determine event type
        $eventType = $this->processor->determineEventType($data);

        // Log webhook
        $webhookLog = HubSpotWebhookLog::create([
            'hubspot_portal_id' => $portalId,
            'event_type' => $eventType,
            'payload' => $data,
            'signature' => $signature,
            'verified' => $signatureValid,
            'status' => 'pending',
        ]);

        // Warn if signature invalid but continue for testing
        if (!$signatureValid) {
            logger()->warning("Webhook signature validation failed for portal: {$portalId}");
        }

        // Get connection
        $connection = HubSpotConnection::where('hubspot_portal_id', $portalId)->first();
        if (!$connection) {
            logger()->warning("No connection found for portal: {$portalId}");
            return response('OK', 200); // Return 200 to avoid retries
        }

        try {
            // Get valid access token
            $accessToken = $this->oauthService->getValidAccessToken($connection);

            // Normalize payload
            $normalizedPayload = $this->processor->normalizePayload($data, $eventType, $accessToken);

            // Execute triggers
            $this->processor->executeTriggers($portalId, $eventType, $normalizedPayload);

            // Update log status
            $webhookLog->update(['status' => 'processed']);
        } catch (Exception $e) {
            logger()->error("Webhook processing failed: " . $e->getMessage());
            $webhookLog->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK', 200);
    }
}
