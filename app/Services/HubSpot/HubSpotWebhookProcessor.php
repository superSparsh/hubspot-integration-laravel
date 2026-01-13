<?php

namespace App\Services\HubSpot;

use App\Models\Trigger;
use App\Models\WebhookPayload;
use App\Services\WapappMessageService;
use Exception;

class HubSpotWebhookProcessor
{
    private HubSpotApiClient $apiClient;
    private WapappMessageService $wapappService;

    public function __construct(HubSpotApiClient $apiClient, WapappMessageService $wapappService)
    {
        $this->apiClient = $apiClient;
        $this->wapappService = $wapappService;
    }

    /**
     * Determine event type from webhook payload
     */
    public function determineEventType(array $payload): string
    {
        if (isset($payload[0]['subscriptionType'])) {
            $subscriptionType = $payload[0]['subscriptionType'];

            $eventMap = [
                'contact.creation' => 'contact.created',
                'contact.deletion' => 'contact.deleted',
                'contact.propertyChange' => 'contact.updated',
                'deal.creation' => 'deal.created',
                'deal.deletion' => 'deal.deleted',
                'deal.propertyChange' => 'deal.updated',
            ];

            return $eventMap[$subscriptionType] ?? $subscriptionType;
        }

        return 'unknown';
    }

    /**
     * Normalize webhook payload to standard format
     */
    public function normalizePayload(array $rawPayload, string $eventType, ?string $accessToken = null): array
    {
        $normalized = [
            'event' => $eventType,
            'portal_id' => $rawPayload[0]['portalId'] ?? null,
            'occurred_at' => isset($rawPayload[0]['occurredAt'])
                ? date('c', $rawPayload[0]['occurredAt'] / 1000)
                : date('c'),
            'raw' => $rawPayload,
        ];

        $objectId = $rawPayload[0]['objectId'] ?? null;
        $objectType = $this->getObjectTypeFromEvent($eventType);

        // Enrich with full object data if possible
        if ($objectId && $accessToken && $objectType) {
            try {
                $fullObject = $this->enrichPayload($objectId, $objectType, $accessToken);
                $normalized[$objectType] = $fullObject;
            } catch (Exception $e) {
                logger()->warning("Failed to enrich payload: " . $e->getMessage());
                $normalized[$objectType] = [
                    'id' => $objectId,
                    'properties' => $this->extractPropertiesFromWebhook($rawPayload[0]),
                ];
            }
        } else {
            $normalized[$objectType] = [
                'id' => $objectId,
                'properties' => $this->extractPropertiesFromWebhook($rawPayload[0]),
            ];
        }

        return $normalized;
    }

    /**
     * Execute matching triggers for this event
     */
    public function executeTriggers(string $portalId, string $eventType, array $payload): void
    {
        // Store payload for field picker
        WebhookPayload::updateOrCreate(
            ['platform_id' => $portalId, 'event' => $eventType],
            ['payload' => $this->flattenForTemplates($payload)]
        );

        // Find matching triggers
        $triggers = Trigger::where('account_id', $portalId)
            ->where('event', $eventType)
            ->with('variables')
            ->get();

        foreach ($triggers as $trigger) {
            try {
                $this->executeTrigger($trigger, $payload);
            } catch (Exception $e) {
                logger()->error("Failed to execute trigger {$trigger->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Execute a single trigger
     */
    private function executeTrigger(Trigger $trigger, array $payload): void
    {
        // Extract recipient phone number
        $to = $this->replacePlaceholders($trigger->to_field, $payload);
        $to = preg_replace('/[^0-9]/', '', $to);

        // Build variables
        $variables = [];
        foreach ($trigger->variables as $var) {
            $variables[$var->var_key] = $this->replacePlaceholders($var->var_path, $payload);
        }

        // Send message
        $this->wapappService->sendMessage(
            $trigger->api_token,
            $trigger->template_uid,
            $to,
            $variables,
            $trigger->id
        );
    }

    /**
     * Replace placeholders in text with actual values
     */
    private function replacePlaceholders(string $text, array $data): string
    {
        return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($data) {
            $keys = explode('.', trim($matches[1]));
            $value = $data;

            foreach ($keys as $key) {
                if (is_numeric($key)) {
                    $key = (int) $key;
                }
                if (!is_array($value) || !array_key_exists($key, $value)) {
                    return '';
                }
                $value = $value[$key];
            }

            return (string) $value;
        }, $text);
    }

    /**
     * Enrich payload by fetching full object
     */
    private function enrichPayload(string $objectId, string $objectType, string $accessToken): array
    {
        if ($objectType === 'contact') {
            return $this->apiClient->getContact($objectId, $accessToken);
        } elseif ($objectType === 'deal') {
            return $this->apiClient->getDeal($objectId, $accessToken);
        }

        throw new Exception("Unsupported object type: {$objectType}");
    }

    /**
     * Get object type from event name
     */
    private function getObjectTypeFromEvent(string $eventType): string
    {
        if (str_starts_with($eventType, 'contact.')) {
            return 'contact';
        } elseif (str_starts_with($eventType, 'deal.')) {
            return 'deal';
        } elseif (str_starts_with($eventType, 'company.')) {
            return 'company';
        }

        return 'unknown';
    }

    /**
     * Extract properties from webhook
     */
    private function extractPropertiesFromWebhook(array $webhookData): array
    {
        $properties = [];

        if (isset($webhookData['propertyName']) && isset($webhookData['propertyValue'])) {
            $properties[$webhookData['propertyName']] = $webhookData['propertyValue'];
        }

        if (isset($webhookData['properties'])) {
            foreach ($webhookData['properties'] as $prop) {
                if (isset($prop['name']) && isset($prop['value'])) {
                    $properties[$prop['name']] = $prop['value'];
                }
            }
        }

        return $properties;
    }

    /**
     * Flatten nested properties for templates
     */
    private function flattenForTemplates(array $normalized): array
    {
        $flattened = $normalized;

        if (isset($normalized['contact']['properties'])) {
            foreach ($normalized['contact']['properties'] as $key => $value) {
                $flattened['contact'][$key] = $value;
            }
        }

        if (isset($normalized['deal']['properties'])) {
            foreach ($normalized['deal']['properties'] as $key => $value) {
                $flattened['deal'][$key] = $value;
            }
        }

        return $flattened;
    }
}
