<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class WapappAuthService
{
    private const API_BASE_URL = 'https://wapapp.tittu.in/api/v1';
    private const SESSION_KEY_USER = 'wapapp_user';
    private const SESSION_KEY_TOKEN = 'wapapp_access_token';
    private const SESSION_KEY_REFRESH = 'wapapp_refresh_token';
    private const SESSION_KEY_EXPIRY = 'wapapp_token_expiry';
    private const SESSION_KEY_EMAIL = 'wapapp_email';
    private const SESSION_KEY_SHOP = 'shop_domain';

    /**
     * Authenticate user with WAPAPP API
     *
     * @param string $email
     * @param string $password
     * @return array{success: bool, message: string, user?: array}
     */
    public function login(string $email, string $password): array
    {
        try {
            $response = Http::asForm()->post(self::API_BASE_URL . '/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

            $data = $response->json();

            if ($response->successful() && !empty($data['success']) && !empty($data['data']['access_token'])) {
                // Store session data
                Session::put(self::SESSION_KEY_EMAIL, $email);
                Session::put(self::SESSION_KEY_TOKEN, $data['data']['access_token']);
                Session::put(self::SESSION_KEY_REFRESH, $data['data']['refresh_token']);
                Session::put(self::SESSION_KEY_EXPIRY, time() + ($data['data']['expires_in'] ?? 3600));
                Session::put(self::SESSION_KEY_USER, $data['data']['user']);
                
                // Use user ID or business ID as shop domain identifier
                $shopDomain = $data['data']['user']['id'] ?? $email;
                Session::put(self::SESSION_KEY_SHOP, $shopDomain);

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $data['data']['user'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Login failed. Please check your credentials.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error. Please try again later.',
            ];
        }
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Session::has(self::SESSION_KEY_EMAIL) && 
               Session::has(self::SESSION_KEY_USER);
    }

    /**
     * Get authenticated user info
     *
     * @return array|null
     */
    public function getUser(): ?array
    {
        return Session::get(self::SESSION_KEY_USER);
    }

    /**
     * Get shop domain (user identifier)
     *
     * @return string|null
     */
    public function getShopDomain(): ?string
    {
        return Session::get(self::SESSION_KEY_SHOP);
    }

    /**
     * Get WAPAPP access token
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return Session::get(self::SESSION_KEY_TOKEN);
    }

    /**
     * Logout user - clear all WAPAPP session data
     *
     * @return void
     */
    public function logout(): void
    {
        Session::forget([
            self::SESSION_KEY_EMAIL,
            self::SESSION_KEY_TOKEN,
            self::SESSION_KEY_REFRESH,
            self::SESSION_KEY_EXPIRY,
            self::SESSION_KEY_USER,
            self::SESSION_KEY_SHOP,
        ]);
    }
}
