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
            'ticket.creation',
            'ticket.deletion',
            'ticket.merge',
            'ticket.restore',
            'ticket.associationChange',
            'company.creation',
            'company.deletion',
            'company.merge',
            'deal.propertyChange',
            'contact.propertyChange',
            'ticket.propertyChange',
            'company.propertyChange',
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
            // Contact events
            'contact.creation' => 'contact.created',
            'contact.deletion' => 'contact.deleted',
            'contact.merge' => 'contact.merged',
            'contact.restore' => 'contact.restored',
            'contact.associationChange' => 'contact.association_changed',
            'contact.privacyDeletion' => 'contact.privacy_deleted',
            // Deal events
            'deal.creation' => 'deal.created',
            'deal.deletion' => 'deal.deleted',
            // Ticket events
            'ticket.creation' => 'ticket.created',
            'ticket.deletion' => 'ticket.deleted',
            'ticket.merge' => 'ticket.merged',
            'ticket.restore' => 'ticket.restored',
            'ticket.associationChange' => 'ticket.association_changed',
            // Company events
            'company.creation' => 'company.created',
            'company.deletion' => 'company.deleted',
            'company.merge' => 'company.merged',
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
            return 'contact.updated';
        }

        // Handle deal property changes
        if ($subscriptionType === 'deal.propertyChange') {
            foreach ($payload as $event) {
                if (isset($event['propertyName'])) {
                    $propertyName = $event['propertyName'];
                    $dealPropertyMap = [
                        'dealstage' => 'deal.stage_changed',
                        'pipeline' => 'deal.pipeline_changed',
                        'amount' => 'deal.amount_changed',
                        'closedate' => 'deal.closedate_changed',
                        'dealname' => 'deal.name_changed',
                        'hubspot_owner_id' => 'deal.owner_changed',
                    ];
                    if (isset($dealPropertyMap[$propertyName])) {
                        return $dealPropertyMap[$propertyName];
                    }
                }
            }
            return 'deal.updated';
        }

        // Handle ticket property changes
        if ($subscriptionType === 'ticket.propertyChange') {
            foreach ($payload as $event) {
                if (isset($event['propertyName'])) {
                    $propertyName = $event['propertyName'];
                    $ticketPropertyMap = [
                        'hs_pipeline_stage' => 'ticket.stage_changed',
                        'hs_pipeline' => 'ticket.pipeline_changed',
                        'hs_ticket_priority' => 'ticket.priority_changed',
                        'hs_ticket_category' => 'ticket.category_changed',
                        'hubspot_owner_id' => 'ticket.owner_changed',
                        'subject' => 'ticket.subject_changed',
                        'content' => 'ticket.content_changed',
                        'hs_resolution' => 'ticket.resolved',
                        'closed_date' => 'ticket.closed',
                        'hs_customer_agent_ticket_status' => 'ticket.status_changed',
                    ];
                    if (isset($ticketPropertyMap[$propertyName])) {
                        return $ticketPropertyMap[$propertyName];
                    }
                }
            }
            return 'ticket.updated';
        }

        // Handle company property changes
        if ($subscriptionType === 'company.propertyChange') {
            foreach ($payload as $event) {
                if (isset($event['propertyName'])) {
                    $propertyName = $event['propertyName'];
                    $companyPropertyMap = [
                        'name' => 'company.name_changed',
                        'domain' => 'company.domain_changed',
                        'industry' => 'company.industry_changed',
                        'annualrevenue' => 'company.revenue_changed',
                        'numberofemployees' => 'company.size_changed',
                        'hubspot_owner_id' => 'company.owner_changed',
                        'lifecyclestage' => 'company.lifecycle_changed',
                        'hs_lead_status' => 'company.status_changed',
                    ];
                    if (isset($companyPropertyMap[$propertyName])) {
                        return $companyPropertyMap[$propertyName];
                    }
                }
            }
            return 'company.updated';
        }

        return $subscriptionType;
    }

    /**
     * Get all supported events for UI dropdown - simplified for better UX
     */
    public static function getSupportedEvents(): array
    {
        return [
            'Contact' => [
                'contact.created' => '👤 Contact Created',
                'contact.updated' => '✏️ Contact Updated (Any)',
                'contact.deleted' => '🗑️ Contact Deleted',
                'contact.phone_changed' => '📱 Phone Changed',
                'contact.email_changed' => '📧 Email Changed',
            ],
            'Deal' => [
                'deal.created' => '💰 Deal Created',
                'deal.updated' => '✏️ Deal Updated (Any)',
                'deal.deleted' => '🗑️ Deal Deleted',
                'deal.stage_changed' => '📊 Deal Stage Changed',
                'deal.amount_changed' => '💵 Amount Changed',
            ],
            'Ticket' => [
                'ticket.created' => '🎫 Ticket Created',
                'ticket.updated' => '✏️ Ticket Updated (Any)',
                'ticket.deleted' => '🗑️ Ticket Deleted',
                'ticket.stage_changed' => '📊 Stage Changed',
                'ticket.priority_changed' => '⚡ Priority Changed',
                'ticket.status_changed' => '🔄 Status Changed',
                'ticket.resolved' => '✅ Ticket Resolved',
                'ticket.closed' => '🔒 Ticket Closed',
            ],
            'Company' => [
                'company.created' => '🏢 Company Created',
                'company.updated' => '✏️ Company Updated (Any)',
                'company.deleted' => '🗑️ Company Deleted',
                'company.merged' => '🔗 Company Merged',
                'company.name_changed' => '📝 Name Changed',
                'company.owner_changed' => '👤 Owner Changed',
                'company.industry_changed' => '🏭 Industry Changed',
                'company.revenue_changed' => '💵 Revenue Changed',
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
        if ($objectId && $accessToken && $objectType && $objectType !== 'unknown') {
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
        } elseif (str_starts_with($eventType, 'ticket.')) {
            return 'ticket';
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

        foreach (['contact', 'deal', 'ticket', 'company'] as $objectType) {
            if (isset($normalized[$objectType]['properties'])) {
                foreach ($normalized[$objectType]['properties'] as $key => $value) {
                    $flattened[$objectType][$key] = $value;
                }
            }
        }

        return $flattened;
    }
}
