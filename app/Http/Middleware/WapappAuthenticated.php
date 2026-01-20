<?php

namespace App\Http\Middleware;

use App\Services\WapappAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WapappAuthenticated
{
    private WapappAuthService $authService;

    public function __construct(WapappAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     * Requires both HubSpot connection AND WAPAPP authentication.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check HubSpot connection first
        if (!session('hubspot_portal_id')) {
            return redirect()->route('home')
                ->with('error', 'Please connect your HubSpot account first.');
        }

        // Then check WAPAPP authentication
        if (!$this->authService->isAuthenticated()) {
            return redirect()->route('wapapp.login')
                ->with('error', 'Please log in to your WAPAPP account.');
        }

        // Share data with all views
        $user = $this->authService->getUser();
        $shopDomain = $this->authService->getShopDomain();
        $portalId = session('hubspot_portal_id');
        
        view()->share('wapappUser', $user);
        view()->share('shopDomain', $shopDomain);
        view()->share('hubspotPortalId', $portalId);

        return $next($request);
    }
}
