<?php

namespace App\Http\Controllers;

use App\Models\HubSpotConnection;
use App\Models\HubSpotWebhookLog;
use App\Services\HubSpot\HubSpotOAuthService;
use App\Services\HubSpot\HubSpotWebhookVerifier;
use App\Services\HubSpot\HubSpotWebhookProcessor;
use Illuminate\Http\Request;
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
            logger()->info('=== WEBHOOK START ===');
            
            $rawPayload = $request->getContent();
            $signature = $request->header('X-HubSpot-Signature')
                ?? $request->header('X-HubSpot-Signature-v3');

            // Validate payload
            if (!$rawPayload) {
                logger()->warning('Empty payload received');
                return response()->json(['status' => 'error', 'message' => 'Empty payload'], 400);
            }

            $data = json_decode($rawPayload, true);
            if (!$data || !is_array($data) || empty($data)) {
                logger()->warning('Invalid JSON: ' . substr($rawPayload, 0, 200));
                return response()->json(['status' => 'error', 'message' => 'Invalid JSON'], 400);
            }

            logger()->info('Payload received: ' . json_encode($data));

            // Extract portal ID
            $portalId = $data[0]['portalId'] ?? null;
            if (!$portalId) {
                logger()->warning('Missing portal ID in payload');
                return response()->json(['status' => 'error', 'message' => 'Missing portal ID'], 400);
            }

            // Determine event type
            $eventType = $this->processor->determineEventType($data);
            logger()->info("Portal: {$portalId}, Event: {$eventType}");

            // Verify signature (log warning if invalid, but continue)
            $signatureValid = false;
            try {
                $signatureValid = $this->verifier->verify($request);
                if (!$signatureValid) {
                    logger()->warning('Signature verification failed');
                }
            } catch (Throwable $e) {
                logger()->warning('Signature check error: ' . $e->getMessage());
            }

            // Log webhook to database
            try {
                $webhookLog = HubSpotWebhookLog::create([
                    'hubspot_portal_id' => (string) $portalId,
                    'event_type' => $eventType,
                    'payload' => $data,
                    'signature' => $signature,
                    'verified' => $signatureValid,
                    'status' => 'pending',
                ]);
                logger()->info("Webhook logged with ID: {$webhookLog->id}");
            } catch (Throwable $e) {
                logger()->error('Failed to log webhook: ' . $e->getMessage());
                $webhookLog = null;
            }

            // Get connection
            $connection = HubSpotConnection::where('hubspot_portal_id', (string) $portalId)->first();
            if (!$connection) {
                logger()->info("No connection found for portal {$portalId}");
                return response()->json(['status' => 'ok', 'message' => 'No connection configured'], 200);
            }

            logger()->info("Connection found: {$connection->id}");

            // Process webhook
            try {
                // Get access token (optional, may fail if not set up)
                $accessToken = null;
                try {
                    if ($connection->access_token && $connection->refresh_token) {
                        $accessToken = $this->oauthService->getValidAccessToken($connection);
                        logger()->info('Access token retrieved');
                    }
                } catch (Throwable $e) {
                    logger()->warning('Could not get access token: ' . $e->getMessage());
                }

                // Normalize payload
                logger()->info('Normalizing payload...');
                $normalizedPayload = $this->processor->normalizePayload($data, $eventType, $accessToken);
                logger()->info('Payload normalized: ' . json_encode(array_keys($normalizedPayload)));

                // Execute triggers
                logger()->info('Executing triggers...');
                $this->processor->executeTriggers((string) $portalId, $eventType, $normalizedPayload);

                // Update log
                if ($webhookLog) {
                    $webhookLog->update(['status' => 'processed']);
                }

                logger()->info('=== WEBHOOK SUCCESS ===');
                return response()->json(['status' => 'ok'], 200);

            } catch (Throwable $e) {
                logger()->error('Processing failed: ' . $e->getMessage());
                logger()->error('Stack trace: ' . $e->getTraceAsString());
                
                if ($webhookLog) {
                    $webhookLog->update([
                        'status' => 'failed',
                        'error' => substr($e->getMessage(), 0, 1000),
                    ]);
                }

                // Still return 200 to avoid HubSpot retries
                return response()->json(['status' => 'ok', 'processed' => false], 200);
            }

        } catch (Throwable $e) {
            logger()->error('FATAL webhook error: ' . $e->getMessage());
            logger()->error('Stack: ' . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
