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
        // Use a placeholder account ID - will be replaced with actual WAPAPP account after login
        $accountId = 'pending_' . bin2hex(random_bytes(8));
        
        // Store our pending account ID in session for later
        session(['pending_hubspot_account' => $accountId]);

        // Build authorize URL (the service handles state management via cache)
        $authData = $this->oauthService->buildAuthorizeUrl($accountId);

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

            if (!$state) {
                return redirect()->route('home')->with('error', 'Invalid OAuth callback - no state parameter');
            }

            // Exchange code for tokens (the service validates state via cache)
            $connection = $this->oauthService->exchangeCodeForToken($code, $state);

            // Store portal ID in session for linking with WAPAPP account
            session([
                'hubspot_portal_id' => $connection->hubspot_portal_id,
                'hubspot_connection_id' => $connection->id,
            ]);

            // Clear pending account from session
            session()->forget('pending_hubspot_account');

            // Redirect to WAPAPP login (Step 2)
            return redirect()->route('wapapp.login')
                ->with('success', 'HubSpot connected! Portal ID: ' . $connection->hubspot_portal_id . '. Now login with your WAPAPP account.');

        } catch (Exception $e) {
            logger()->error('OAuth callback failed: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Failed to connect HubSpot: ' . $e->getMessage());
        }
    }
}
