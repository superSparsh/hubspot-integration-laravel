<?php

namespace App\Http\Controllers;

use App\Models\HubSpotConnection;
use App\Services\WapappAuthService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WapappAuthController extends Controller
{
    private WapappAuthService $authService;

    public function __construct(WapappAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the WAPAPP login form (Step 2 - after HubSpot OAuth)
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Check if HubSpot is connected first
        $portalId = session('hubspot_portal_id');
        
        if (!$portalId) {
            return redirect()->route('home')
                ->with('error', 'Please connect your HubSpot account first.');
        }

        // If already authenticated with WAPAPP, redirect to dashboard
        if ($this->authService->isAuthenticated()) {
            return redirect()->route('dashboard');
        }

        return view('wapapp.login', [
            'hubspotPortalId' => $portalId,
        ]);
    }

    /**
     * Handle login request - Link WAPAPP account to HubSpot connection
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:1',
        ]);

        // Verify HubSpot is connected
        $portalId = session('hubspot_portal_id');
        $connectionId = session('hubspot_connection_id');

        if (!$portalId) {
            return redirect()->route('home')
                ->with('error', 'HubSpot connection lost. Please reconnect.');
        }

        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        if ($result['success']) {
            // Link WAPAPP account to the HubSpot connection
            $shopDomain = $this->authService->getShopDomain();
            
            if ($connectionId) {
                $connection = HubSpotConnection::find($connectionId);
                if ($connection) {
                    $connection->wapapp_account_id = $shopDomain;
                    $connection->save();
                }
            }

            return redirect()->route('dashboard')
                ->with('success', 'Welcome! Your HubSpot and WAPAPP accounts are now linked.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $result['message']]);
    }

    /**
     * Handle logout request
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        // Clear HubSpot session data too
        session()->forget(['hubspot_portal_id', 'hubspot_connection_id']);

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
