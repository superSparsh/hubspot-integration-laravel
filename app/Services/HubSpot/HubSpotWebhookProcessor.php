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
        if (empty($payload)) {
            return 'unknown';
        }

        // Priority order: creation events first, then property changes
        $priorityOrder = [
            'contact.creation',
            'contact.privacyDeletion',
            'deal.creation',
            'contact.propertyChange',
            'deal.propertyChange',
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
            'contact.privacyDeletion' => 'contact.privacy_deleted',
            'deal.creation' => 'deal.created',
        ];

        if (isset($eventMap[$subscriptionType])) {
            return $eventMap[$subscriptionType];
        }

        // Handle contact property changes
        if ($subscriptionType === 'contact.propertyChange') {
            foreach ($payload as $event) {
                if (isset($event['propertyName'])) {
                    $propertyName = $event['propertyName'];
                    $propertyEventMap = [
                        'firstname' => 'contact.name_changed',
                        'lastname' => 'contact.name_changed',
                        'phone' => 'contact.phone_changed',
                        'mobilephone' => 'contact.phone_changed',
                        'hs_whatsapp_phone_number' => 'contact.whatsapp_changed',
                        'work_email' => 'contact.email_changed',
                        'email' => 'contact.email_changed',
                        'lifecyclestage' => 'contact.lifecycle_changed',
                        'hs_lead_status' => 'contact.status_changed',
                        'hubspot_owner_id' => 'contact.owner_changed',
                    ];
                    if (isset($propertyEventMap[$propertyName])) {
                        return $propertyEventMap[$propertyName];
                    }
                }
            }
            return 'contact.updated';
        }

        // Handle deal property changes
        if ($subscriptionType === 'deal.propertyChange') {
            foreach ($payload as $event) {
                if (isset($event['propertyName'])) {
                    $propertyName = $event['propertyName'];
                    $dealPropertyMap = [
                        'dealname' => 'deal.name_changed',
                        'dealstage' => 'deal.stage_changed',
                        'amount' => 'deal.amount_changed',
                        'pipeline' => 'deal.pipeline_changed',
                        'closedate' => 'deal.closedate_changed',
                        'hubspot_owner_id' => 'deal.owner_changed',
                    ];
                    if (isset($dealPropertyMap[$propertyName])) {
                        return $dealPropertyMap[$propertyName];
                    }
                }
            }
            return 'deal.updated';
        }

        return $subscriptionType;
    }

    /**
     * Get all supported events for UI dropdown
     */
    public static function getSupportedEvents(): array
    {
        return [
            'Contact' => [
                'contact.created' => '👤 Contact Created',
                'contact.updated' => '✏️ Any Contact Update',
                'contact.privacy_deleted' => '🔒 Deleted for Privacy',
                'contact.name_changed' => '📝 Name Changed',
                'contact.phone_changed' => '📱 Phone Changed',
                'contact.whatsapp_changed' => '💬 WhatsApp Changed',
                'contact.email_changed' => '📧 Email Changed',
                'contact.lifecycle_changed' => '🔄 Lifecycle Changed',
                'contact.status_changed' => '📊 Lead Status Changed',
                'contact.owner_changed' => '👤 Owner Changed',
            ],
            'Deal' => [
                'deal.created' => '💰 Deal Created',
                'deal.updated' => '✏️ Any Deal Update',
                'deal.name_changed' => '📝 Deal Name Changed',
                'deal.stage_changed' => '📊 Stage Changed',
                'deal.amount_changed' => '💵 Amount Changed',
                'deal.pipeline_changed' => '📂 Pipeline Changed',
                'deal.closedate_changed' => '📅 Close Date Changed',
                'deal.owner_changed' => '👤 Owner Changed',
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
        if ($objectId && $accessToken && $objectType !== 'unknown') {
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
            if ($objectType !== 'unknown') {
                $normalized[$objectType] = [
                    'id' => $objectId,
                    'properties' => $this->extractPropertiesFromWebhook($rawPayload),
                ];
            }
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
        }

        return $properties;
    }

    /**
     * Flatten nested properties for templates
     */
    private function flattenForTemplates(array $normalized): array
    {
        $flattened = $normalized;

        foreach (['contact', 'deal'] as $objectType) {
            if (isset($normalized[$objectType]['properties'])) {
                foreach ($normalized[$objectType]['properties'] as $key => $value) {
                    $flattened[$objectType][$key] = $value;
                }
            }
        }

        return $flattened;
    }
}
