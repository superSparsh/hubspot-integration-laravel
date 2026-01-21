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
     * HubSpot sends batched events - we prioritize lifecycle events over property changes
     */
    public function determineEventType(array $payload): string
    {
        if (empty($payload)) {
            return 'unknown';
        }

        // Priority order: lifecycle events first, then property changes
        $priorityOrder = [
            'contact.creation',
            'contact.deletion',
            'contact.merge',
            'contact.restore',
            'contact.associationChange',
            'contact.privacyDeletion',
            'deal.creation',
            'deal.deletion',
            'deal.propertyChange',
            'contact.propertyChange',
        ];

        $foundEvents = [];
        foreach ($payload as $event) {
            if (isset($event['subscriptionType'])) {
                $foundEvents[] = $event['subscriptionType'];
            }
        }

        // Find highest priority event
        foreach ($priorityOrder as $priority) {
            if (in_array($priority, $foundEvents)) {
                return $this->mapEventType($priority, $payload);
            }
        }

        // Fallback to first event
        return $this->mapEventType($foundEvents[0] ?? 'unknown', $payload);
    }

    /**
     * Map HubSpot subscription type to our event naming
     */
    private function mapEventType(string $subscriptionType, array $payload = []): string
    {
        // Basic event mapping
        $eventMap = [
            'contact.creation' => 'contact.created',
            'contact.deletion' => 'contact.deleted',
            'contact.merge' => 'contact.merged',
            'contact.restore' => 'contact.restored',
            'contact.associationChange' => 'contact.association_changed',
            'contact.privacyDeletion' => 'contact.privacy_deleted',
            'deal.creation' => 'deal.created',
            'deal.deletion' => 'deal.deleted',
            'deal.propertyChange' => 'deal.updated',
        ];

        if (isset($eventMap[$subscriptionType])) {
            return $eventMap[$subscriptionType];
        }

        // Handle property changes with specific property names
        if ($subscriptionType === 'contact.propertyChange') {
            // Check for specific property changes
            foreach ($payload as $event) {
                if (isset($event['propertyName'])) {
                    $propertyName = $event['propertyName'];
                    
                    // Map common property changes to specific events
                    $propertyEventMap = [
                        'email' => 'contact.email_changed',
                        'phone' => 'contact.phone_changed',
                        'mobilephone' => 'contact.phone_changed',
                        'hs_whatsapp_phone_number' => 'contact.whatsapp_changed',
                        'firstname' => 'contact.name_changed',
                        'lastname' => 'contact.name_changed',
                        'lifecyclestage' => 'contact.lifecycle_changed',
                        'hs_lead_status' => 'contact.status_changed',
                    ];

                    if (isset($propertyEventMap[$propertyName])) {
                        return $propertyEventMap[$propertyName];
                    }
                }
            }
            
            // Generic update if no specific property match
            return 'contact.updated';
        }

        return $subscriptionType;
    }

    /**
     * Get all supported events for UI dropdown
     */
    public static function getSupportedEvents(): array
    {
        return [
            'Contact Lifecycle' => [
                'contact.created' => 'Contact Created',
                'contact.deleted' => 'Contact Deleted',
                'contact.merged' => 'Contact Merged',
                'contact.restored' => 'Contact Restored',
            ],
            'Contact Updates' => [
                'contact.updated' => 'Any Property Changed',
                'contact.email_changed' => 'Email Changed',
                'contact.phone_changed' => 'Phone Changed',
                'contact.whatsapp_changed' => 'WhatsApp Number Changed',
                'contact.name_changed' => 'Name Changed',
                'contact.lifecycle_changed' => 'Lifecycle Stage Changed',
                'contact.status_changed' => 'Lead Status Changed',
            ],
            'Contact Other' => [
                'contact.association_changed' => 'Association Changed',
                'contact.privacy_deleted' => 'Deleted for Privacy',
            ],
            'Deal Events' => [
                'deal.created' => 'Deal Created',
                'deal.deleted' => 'Deal Deleted',
                'deal.updated' => 'Deal Updated',
            ],
        ];
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
                    'properties' => $this->extractPropertiesFromWebhook($rawPayload),
                ];
            }
        } else {
            $normalized[$objectType] = [
                'id' => $objectId,
                'properties' => $this->extractPropertiesFromWebhook($rawPayload),
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

        // Find matching triggers (account_id = HubSpot portal ID)
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
     * Extract properties from webhook payload array
     */
    private function extractPropertiesFromWebhook(array $rawPayload): array
    {
        $properties = [];

        foreach ($rawPayload as $event) {
            if (isset($event['propertyName']) && isset($event['propertyValue'])) {
                $properties[$event['propertyName']] = $event['propertyValue'];
            }

            if (isset($event['properties'])) {
                foreach ($event['properties'] as $prop) {
                    if (isset($prop['name']) && isset($prop['value'])) {
                        $properties[$prop['name']] = $prop['value'];
                    }
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
