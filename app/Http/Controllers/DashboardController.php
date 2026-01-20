<?php

namespace App\Http\Controllers;

use App\Models\HubSpotConnection;
use App\Models\Trigger;
use App\Services\WapappAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private WapappAuthService $authService;

    public function __construct(WapappAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display the dashboard with triggers and settings
     */
    public function index(): View
    {
        $user = $this->authService->getUser();
        $shopDomain = $this->authService->getShopDomain();
        $portalId = session('hubspot_portal_id');

        // Get HubSpot connection - look up by portal_id first, then by account_id
        $connection = HubSpotConnection::where('hubspot_portal_id', $portalId)
            ->orWhere('wapapp_account_id', $shopDomain)
            ->first();

        // Get WAPAPP API token from connection
        $wapappToken = $connection?->wapapp_token;
        $tokenExists = !empty($wapappToken);

        // Get triggers for this shop if token exists
        $triggers = [];
        if ($tokenExists) {
            $triggers = Trigger::where('shop_domain', $shopDomain)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('dashboard.index', [
            'user' => $user,
            'shopDomain' => $shopDomain,
            'connection' => $connection,
            'wapappToken' => $wapappToken,
            'tokenExists' => $tokenExists,
            'triggers' => $triggers,
            'hubspotPortalId' => $portalId,
        ]);
    }

    /**
     * Update the WAPAPP API token
     */
    public function updateApiToken(Request $request): RedirectResponse
    {
        $request->validate([
            'api_token' => 'required|string|min:10',
        ]);

        $shopDomain = $this->authService->getShopDomain();
        $portalId = session('hubspot_portal_id');

        if (!$shopDomain || !$portalId) {
            return back()->with('error', 'Invalid session. Please log in again.');
        }

        // Find connection by portal_id or account_id
        $connection = HubSpotConnection::where('hubspot_portal_id', $portalId)
            ->orWhere('wapapp_account_id', $shopDomain)
            ->first();

        if (!$connection) {
            return back()->with('error', 'HubSpot connection not found. Please reconnect.');
        }

        // Update the WAPAPP token
        $connection->wapapp_token = $request->input('api_token');
        $connection->wapapp_account_id = $shopDomain;
        $connection->save();

        return back()->with('success', 'API token saved successfully!');
    }
}
