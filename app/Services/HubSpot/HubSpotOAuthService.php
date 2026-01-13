<?php

namespace App\Services\HubSpot;

use App\Models\HubSpotConnection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Exception;

class HubSpotOAuthService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $scopes;

    public function __construct()
    {
        $this->clientId = config('hubspot.client_id');
        $this->clientSecret = config('hubspot.client_secret');
        $this->redirectUri = config('hubspot.redirect_uri');
        $this->scopes = config('hubspot.scopes');
    }

    /**
     * Build HubSpot OAuth authorization URL
     */
    public function buildAuthorizeUrl(string $accountId): array
    {
        $state = Str::random(40);

        // Store state in cache for 10 minutes
        cache()->put("hubspot_oauth_state_{$state}", $accountId, now()->addMinutes(10));

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scopes,
            'state' => $state,
        ];

        return [
            'url' => 'https://app.hubspot.com/oauth/authorize?' . http_build_query($params),
            'state' => $state
        ];
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCodeForToken(string $code, string $state): HubSpotConnection
    {
        // Validate state
        $accountId = cache()->pull("hubspot_oauth_state_{$state}");
        if (!$accountId) {
            throw new Exception('Invalid or expired OAuth state');
        }

        $tokenUrl = 'https://api.hubapi.com/oauth/v1/token';

        $postData = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ];

        $response = $this->makeTokenRequest($tokenUrl, $postData);

        // Get portal ID
        $portalId = $this->getPortalId($response['access_token']);

        // Store connection
        return $this->storeConnection($portalId, $response, $accountId);
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken(HubSpotConnection $connection): HubSpotConnection
    {
        $refreshToken = Crypt::decryptString($connection->refresh_token);
        $tokenUrl = 'https://api.hubapi.com/oauth/v1/token';

        $postData = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ];

        $response = $this->makeTokenRequest($tokenUrl, $postData);

        // Update connection with new tokens
        $connection->update([
            'access_token' => Crypt::encryptString($response['access_token']),
            'refresh_token' => Crypt::encryptString($response['refresh_token']),
            'expires_at' => now()->addSeconds($response['expires_in']),
        ]);

        return $connection->fresh();
    }

    /**
     * Get valid access token (refresh if needed)
     */
    public function getValidAccessToken(HubSpotConnection $connection): string
    {
        // Refresh if token expires in less than 5 minutes
        if ($connection->expires_at->subMinutes(5)->isPast()) {
            $connection = $this->refreshAccessToken($connection);
        }

        return Crypt::decryptString($connection->access_token);
    }

    /**
     * Get portal ID from access token
     */
    private function getPortalId(string $accessToken): string
    {
        $ch = curl_init('https://api.hubapi.com/oauth/v1/access-tokens/' . $accessToken);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to get portal ID: $response");
        }

        $data = json_decode($response, true);
        return (string) $data['hub_id'];
    }

    /**
     * Store or update HubSpot connection
     */
    private function storeConnection(string $portalId, array $tokens, string $accountId): HubSpotConnection
    {
        return HubSpotConnection::updateOrCreate(
            ['hubspot_portal_id' => $portalId],
            [
                'wapapp_account_id' => $accountId,
                'access_token' => Crypt::encryptString($tokens['access_token']),
                'refresh_token' => Crypt::encryptString($tokens['refresh_token']),
                'expires_at' => now()->addSeconds($tokens['expires_in']),
                'scopes' => $this->scopes,
                'status' => 'active',
            ]
        );
    }

    /**
     * Make token request to HubSpot
     */
    private function makeTokenRequest(string $url, array $postData): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Token request failed: $response");
        }

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            throw new Exception("No access token in response: $response");
        }

        return $data;
    }
}
