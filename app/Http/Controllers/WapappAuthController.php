<?php

namespace App\Http\Controllers;

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
     * Show the WAPAPP login form
     *
     * @return View|RedirectResponse
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // If already authenticated, redirect to dashboard
        if ($this->authService->isAuthenticated()) {
            return redirect()->route('dashboard');
        }

        return view('wapapp.login');
    }

    /**
     * Handle login request
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:1',
        ]);

        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        if ($result['success']) {
            return redirect()->route('dashboard')
                ->with('success', 'Welcome back, ' . ($result['user']['first_name'] ?? 'User') . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $result['message']]);
    }

    /**
     * Handle logout request
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('wapapp.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
