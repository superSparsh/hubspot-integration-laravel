<?php

namespace App\Services\HubSpot;

use Exception;

class HubSpotApiClient
{
    private string $apiBase;
    private int $maxRetries = 3;

    public function __construct()
    {
        $this->apiBase = config('hubspot.api_base', 'https://api.hubapi.com');
    }

    /**
     * Get contact by ID
     */
    public function getContact(string $contactId, string $accessToken, array $properties = []): array
    {
        $endpoint = "/crm/v3/objects/contacts/{$contactId}";

        if (!empty($properties)) {
            $endpoint .= '?properties=' . implode(',', $properties);
        }

        return $this->makeRequest($endpoint, $accessToken);
    }

    /**
     * Get deal by ID
     */
    public function getDeal(string $dealId, string $accessToken, array $properties = []): array
    {
        $endpoint = "/crm/v3/objects/deals/{$dealId}";

        if (!empty($properties)) {
            $endpoint .= '?properties=' . implode(',', $properties);
        }

        return $this->makeRequest($endpoint, $accessToken);
    }

    /**
     * Make HTTP request with retry logic and rate limiting
     */
    public function makeRequest(string $endpoint, string $accessToken, string $method = 'GET', ?array $data = null): array
    {
        $url = $this->apiBase . $endpoint;
        $attempt = 0;
        $lastError = null;

        while ($attempt < $this->maxRetries) {
            $attempt++;

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$accessToken}",
                    'Content-Type: application/json',
                ],
            ]);

            if ($method === 'POST' && $data) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Success
            if ($httpCode >= 200 && $httpCode < 300) {
                return json_decode($response, true);
            }

            // Rate limit - exponential backoff
            if ($httpCode === 429) {
                $retryAfter = $this->getRetryAfter($response);
                $lastError = "Rate limit exceeded, retrying after {$retryAfter}s";

                if ($attempt < $this->maxRetries) {
                    sleep($retryAfter);
                    continue;
                }
            }

            // Unauthorized
            if ($httpCode === 401) {
                throw new Exception("Unauthorized: Token may be expired");
            }

            $lastError = "API request failed with code {$httpCode}: {$response}";

            // Don't retry client errors (except 429)
            if ($httpCode >= 400 && $httpCode < 500 && $httpCode !== 429) {
                break;
            }

            // Exponential backoff for server errors
            if ($httpCode >= 500 && $attempt < $this->maxRetries) {
                sleep(pow(2, $attempt - 1));
            }
        }

        throw new Exception($lastError ?: "API request failed after {$this->maxRetries} attempts");
    }

    /**
     * Extract retry-after from rate limit response
     */
    private function getRetryAfter(string $response): int
    {
        $data = json_decode($response, true);

        if (isset($data['policyName']) && str_contains($data['policyName'], 'SECONDLY')) {
            return 1;
        }

        if (isset($data['policyName']) && str_contains($data['policyName'], 'DAILY')) {
            return 60;
        }

        return 2;
    }
}
