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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->authService->isAuthenticated()) {
            return redirect()->route('wapapp.login')
                ->with('error', 'Please log in to your WAPAPP account to continue.');
        }

        // Share user data with all views
        $user = $this->authService->getUser();
        $shopDomain = $this->authService->getShopDomain();
        
        view()->share('wapappUser', $user);
        view()->share('shopDomain', $shopDomain);

        return $next($request);
    }
}
