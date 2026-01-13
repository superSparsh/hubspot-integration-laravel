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
     * Initiate OAuth flow
     */
    public function connect(Request $request)
    {
        $accountId = session('wapapp_account_id') ?? 'default';

        $authData = $this->oauthService->buildAuthorizeUrl($accountId);

        return redirect($authData['url']);
    }

    /**
     * Handle OAuth callback
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->input('code');
            $state = $request->input('state');

            if (!$code || !$state) {
                return redirect('/')->with('error', 'Invalid OAuth callback');
            }

            // Exchange code for tokens and store connection
            $connection = $this->oauthService->exchangeCodeForToken($code, $state);

            return redirect('/')
                ->with('success', 'HubSpot connected successfully! Portal ID: ' . $connection->hubspot_portal_id);
        } catch (Exception $e) {
            logger()->error('OAuth callback failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'Failed to connect HubSpot: ' . $e->getMessage());
        }
    }
}
