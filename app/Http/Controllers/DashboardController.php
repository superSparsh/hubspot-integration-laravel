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

        // Get HubSpot connection for this shop
        $connection = HubSpotConnection::where('wapapp_account_id', $shopDomain)->first();

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

        if (!$shopDomain) {
            return back()->with('error', 'Invalid session. Please log in again.');
        }

        // Find or create the connection
        $connection = HubSpotConnection::firstOrCreate(
            ['wapapp_account_id' => $shopDomain],
            ['status' => 'active']
        );

        // Update the WAPAPP token
        $connection->wapapp_token = $request->input('api_token');
        $connection->save();

        return back()->with('success', 'API token saved successfully!');
    }
}
