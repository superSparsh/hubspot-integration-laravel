<?php

namespace App\Http\Controllers;

use App\Services\HubSpot\HubSpotOAuthService;
use Illuminate\Http\Request;
use Exception;

class HubSpotAuthController extends Controller
{
    private HubSpotOAuthService $oauthService;

    public function __construct(HubSpotOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    /**
     * Initiate OAuth flow - Step 1: Connect to HubSpot
     */
    public function connect(Request $request)
    {
        // Generate a unique state for this session
        $state = bin2hex(random_bytes(16));
        session(['hubspot_oauth_state' => $state]);

        $authData = $this->oauthService->buildAuthorizeUrl($state);

        return redirect($authData['url']);
    }

    /**
     * Handle OAuth callback - Store HubSpot connection and redirect to WAPAPP login
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->input('code');
            $state = $request->input('state');

            if (!$code) {
                return redirect()->route('home')->with('error', 'Invalid OAuth callback - no authorization code');
            }

            // Verify state matches
            $expectedState = session('hubspot_oauth_state');
            if ($state !== $expectedState) {
                return redirect()->route('home')->with('error', 'Invalid OAuth state - possible CSRF attack');
            }

            // Exchange code for tokens and store connection
            $connection = $this->oauthService->exchangeCodeForToken($code, $state);

            // Store portal ID in session for linking with WAPAPP account
            session([
                'hubspot_portal_id' => $connection->hubspot_portal_id,
                'hubspot_connection_id' => $connection->id,
            ]);

            // Clear OAuth state
            session()->forget('hubspot_oauth_state');

            // Redirect to WAPAPP login (Step 2)
            return redirect()->route('wapapp.login')
                ->with('success', 'HubSpot connected! Portal ID: ' . $connection->hubspot_portal_id . '. Now login with your WAPAPP account.');

        } catch (Exception $e) {
            logger()->error('OAuth callback failed: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Failed to connect HubSpot: ' . $e->getMessage());
        }
    }
}
