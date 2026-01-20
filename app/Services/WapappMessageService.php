<?php

namespace App\Services;

use App\Models\MessageLog;
use Exception;

class WapappMessageService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.wapapp.api_url', 'https://wapapp.tittu.in/api/v1');
    }

    /**
     * Send WhatsApp message via WAPAPP API
     */
    public function sendMessage(string $apiToken, string $templateUid, string $to, array $variables, ?int $triggerId = null): array
    {
        $url = "{$this->apiUrl}/directmessage";

        $postData = [
            'api_token' => $apiToken,
            'template_uid' => $templateUid,
            'to' => $to,
        ];

        foreach ($variables as $key => $value) {
            $postData[$key] = $value;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log message
        MessageLog::create([
            'trigger_id' => $triggerId,
            'recipient' => $to,
            'response_code' => $httpCode,
            'response_body' => $response,
        ]);

        if ($httpCode !== 200) {
            throw new Exception("WAPAPP API request failed with code {$httpCode}: {$response}");
        }

        return json_decode($response, true);
    }

    /**
     * Fetch templates from WAPAPP API
     */
    public function fetchTemplates(string $apiToken): array
    {
        $url = "{$this->apiUrl}/templates?api_token=" . urlencode($apiToken);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return [];
        }

        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    }
}
