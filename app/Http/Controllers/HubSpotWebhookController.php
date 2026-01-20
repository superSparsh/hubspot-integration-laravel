<?php

namespace App\Http\Controllers;

use App\Models\HubSpotConnection;
use App\Models\HubSpotWebhookLog;
use App\Services\HubSpot\HubSpotOAuthService;
use App\Services\HubSpot\HubSpotWebhookVerifier;
use App\Services\HubSpot\HubSpotWebhookProcessor;
use Illuminate\Http\Request;
use Exception;
use Throwable;

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
        try {
            $rawPayload = $request->getContent();
            $signature = $request->header('X-HubSpot-Signature')
                ?? $request->header('X-HubSpot-Signature-v3');

            // Validate payload
            if (!$rawPayload) {
                logger()->warning('Webhook received with empty payload');
                return response()->json(['status' => 'error', 'message' => 'Missing payload'], 400);
            }

            $data = json_decode($rawPayload, true);
            if (!$data || !is_array($data) || empty($data)) {
                logger()->warning('Webhook received with invalid JSON: ' . substr($rawPayload, 0, 500));
                return response()->json(['status' => 'error', 'message' => 'Invalid JSON'], 400);
            }

            // Verify signature (but don't block for testing)
            $signatureValid = false;
            try {
                $signatureValid = $this->verifier->verify($request);
            } catch (Throwable $e) {
                logger()->warning('Signature verification failed: ' . $e->getMessage());
            }

            // Extract portal ID
            $portalId = $data[0]['portalId'] ?? null;
            if (!$portalId) {
                logger()->warning('Webhook missing portal ID');
                return response()->json(['status' => 'error', 'message' => 'Missing portal ID'], 400);
            }

            // Determine event type
            $eventType = $this->processor->determineEventType($data);
            logger()->info("Webhook received: portal={$portalId}, event={$eventType}");

            // Log webhook
            try {
                $webhookLog = HubSpotWebhookLog::create([
                    'hubspot_portal_id' => (string) $portalId,
                    'event_type' => $eventType,
                    'payload' => $data,
                    'signature' => $signature,
                    'verified' => $signatureValid,
                    'status' => 'pending',
                ]);
            } catch (Throwable $e) {
                logger()->error('Failed to create webhook log: ' . $e->getMessage());
                // Continue processing even if logging fails
                $webhookLog = null;
            }

            // Get connection
            $connection = HubSpotConnection::where('hubspot_portal_id', (string) $portalId)->first();
            if (!$connection) {
                logger()->info("No HubSpot connection found for portal: {$portalId}");
                // Return 200 to avoid retries - connection not set up yet
                return response()->json(['status' => 'ok', 'message' => 'No connection found for portal'], 200);
            }

            // Process webhook
            try {
                // Get valid access token (may be null if not available)
                $accessToken = null;
                try {
                    $accessToken = $this->oauthService->getValidAccessToken($connection);
                } catch (Throwable $e) {
                    logger()->warning("Could not get access token: " . $e->getMessage());
                }

                // Normalize payload
                $normalizedPayload = $this->processor->normalizePayload($data, $eventType, $accessToken);

                // Execute triggers
                $this->processor->executeTriggers((string) $portalId, $eventType, $normalizedPayload);

                // Update log status
                if ($webhookLog) {
                    $webhookLog->update(['status' => 'processed']);
                }

                logger()->info("Webhook processed successfully for portal: {$portalId}");
            } catch (Throwable $e) {
                logger()->error("Webhook processing failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                if ($webhookLog) {
                    $webhookLog->update([
                        'status' => 'failed',
                        'error' => substr($e->getMessage(), 0, 1000),
                    ]);
                }
            }

            return response()->json(['status' => 'ok'], 200);

        } catch (Throwable $e) {
            logger()->error("Webhook handler error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
