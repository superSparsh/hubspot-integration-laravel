<?php

namespace App\Http\Controllers;

use App\Models\HubSpotConnection;
use App\Models\Trigger;
use App\Models\TriggerVariable;
use App\Models\WebhookPayload;
use App\Services\WapappAuthService;
use App\Services\WapappMessageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class TriggerController extends Controller
{
    private WapappAuthService $authService;
    private WapappMessageService $wapappService;

    public function __construct(WapappAuthService $authService, WapappMessageService $wapappService)
    {
        $this->authService = $authService;
        $this->wapappService = $wapappService;
    }

    /**
     * Show the form for creating a new trigger
     */
    public function create(): View|RedirectResponse
    {
        $shopDomain = $this->authService->getShopDomain();
        
        // Get WAPAPP token
        $connection = HubSpotConnection::where('wapapp_shop_id', $shopDomain)->first();
        $token = $connection?->wapapp_token;

        if (!$token) {
            return redirect()->route('dashboard')
                ->with('error', 'Please set your WAPAPP API token first.');
        }

        // Fetch templates from WAPAPP
        $templates = $this->wapappService->fetchTemplates($token);

        return view('triggers.create', [
            'templates' => $templates,
            'token' => $token,
        ]);
    }

    /**
     * Store a newly created trigger
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'trigger_name' => 'required|string|min:10|max:255',
            'event' => 'required|string',
            'template_uid' => 'required|string',
            'template_name' => 'required|string',
            'to' => 'required|string',
            'api_token' => 'required|string',
        ]);

        $shopDomain = $this->authService->getShopDomain();

        // Create trigger
        $trigger = Trigger::create([
            'uuid' => (string) Str::uuid(),
            'shop_domain' => $shopDomain,
            'event' => $request->input('event'),
            'trigger_name' => $request->input('trigger_name'),
            'template_uid' => $request->input('template_uid'),
            'template_name' => $request->input('template_name'),
            'to_field' => $request->input('to'),
            'api_token' => $request->input('api_token'),
            'integration_type' => 'hubspot',
        ]);

        // Store variables if any
        $varKeys = $request->input('vars.keys', []);
        $varValues = $request->input('vars.values', []);

        foreach (array_map(null, $varKeys, $varValues) as [$key, $value]) {
            if (!empty(trim($key ?? ''))) {
                TriggerVariable::create([
                    'trigger_id' => $trigger->id,
                    'var_key' => $key,
                    'var_path' => $value,
                ]);
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Trigger created successfully!');
    }

    /**
     * Show the form for editing a trigger
     */
    public function edit(string $uuid): View|RedirectResponse
    {
        $shopDomain = $this->authService->getShopDomain();

        $trigger = Trigger::where('uuid', $uuid)
            ->where('shop_domain', $shopDomain)
            ->with('variables')
            ->first();

        if (!$trigger) {
            return redirect()->route('dashboard')
                ->with('error', 'Trigger not found.');
        }

        // Get WAPAPP token
        $connection = HubSpotConnection::where('wapapp_shop_id', $shopDomain)->first();
        $token = $connection?->wapapp_token;

        if (!$token) {
            return redirect()->route('dashboard')
                ->with('error', 'Please set your WAPAPP API token first.');
        }

        // Fetch templates from WAPAPP
        $templates = $this->wapappService->fetchTemplates($token);

        return view('triggers.edit', [
            'trigger' => $trigger,
            'variables' => $trigger->variables,
            'templates' => $templates,
            'token' => $token,
        ]);
    }

    /**
     * Update the specified trigger
     */
    public function update(Request $request, string $uuid): RedirectResponse
    {
        $request->validate([
            'trigger_name' => 'required|string|min:10|max:255',
            'event' => 'required|string',
            'template_uid' => 'required|string',
            'template_name' => 'required|string',
            'to' => 'required|string',
        ]);

        $shopDomain = $this->authService->getShopDomain();

        $trigger = Trigger::where('uuid', $uuid)
            ->where('shop_domain', $shopDomain)
            ->first();

        if (!$trigger) {
            return redirect()->route('dashboard')
                ->with('error', 'Trigger not found.');
        }

        // Update trigger
        $trigger->update([
            'event' => $request->input('event'),
            'trigger_name' => $request->input('trigger_name'),
            'template_uid' => $request->input('template_uid'),
            'template_name' => $request->input('template_name'),
            'to_field' => $request->input('to'),
        ]);

        // Delete old variables and create new ones
        TriggerVariable::where('trigger_id', $trigger->id)->delete();

        $varKeys = $request->input('vars.keys', []);
        $varValues = $request->input('vars.values', []);

        foreach (array_map(null, $varKeys, $varValues) as [$key, $value]) {
            if (!empty(trim($key ?? ''))) {
                TriggerVariable::create([
                    'trigger_id' => $trigger->id,
                    'var_key' => $key,
                    'var_path' => $value,
                ]);
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Trigger updated successfully!');
    }

    /**
     * Remove the specified trigger
     */
    public function destroy(int $id): RedirectResponse
    {
        $shopDomain = $this->authService->getShopDomain();

        $trigger = Trigger::where('id', $id)
            ->where('shop_domain', $shopDomain)
            ->first();

        if (!$trigger) {
            return redirect()->route('dashboard')
                ->with('error', 'Trigger not found.');
        }

        // Delete variables first
        TriggerVariable::where('trigger_id', $trigger->id)->delete();
        
        // Delete trigger
        $trigger->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Trigger deleted successfully.');
    }

    /**
     * Test a trigger using stored webhook payload
     */
    public function test(int $id): RedirectResponse
    {
        $shopDomain = $this->authService->getShopDomain();

        $trigger = Trigger::where('id', $id)
            ->where('shop_domain', $shopDomain)
            ->with('variables')
            ->first();

        if (!$trigger) {
            return redirect()->route('dashboard')
                ->with('error', 'Trigger not found.');
        }

        // Get latest webhook payload for this event
        $payload = WebhookPayload::where('platform_id', $shopDomain)
            ->where('event', $trigger->event)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$payload) {
            return redirect()->route('dashboard')
                ->with('error', 'No webhook payload found for this event. Please trigger the event in HubSpot first.');
        }

        $data = $payload->payload;

        // Replace placeholders in 'to' field
        $to = preg_replace('/[^0-9]/', '', $this->replacePlaceholders($trigger->to_field, $data));

        // Build variables
        $vars = [];
        foreach ($trigger->variables as $variable) {
            $vars[$variable->var_key] = $this->replacePlaceholders($variable->var_path, $data);
        }

        try {
            $response = $this->wapappService->sendMessage(
                $trigger->api_token,
                $trigger->template_uid,
                $to,
                $vars,
                $trigger->id
            );

            if (isset($response['status']) && $response['status'] == 1) {
                return redirect()->route('dashboard')
                    ->with('success', 'Test message sent successfully! ID: ' . ($response['message_id'] ?? 'N/A'));
            }

            return redirect()->route('dashboard')
                ->with('error', 'Message failed: ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Test failed: ' . $e->getMessage());
        }
    }

    /**
     * Get payload fields for dynamic field picker (API endpoint)
     */
    public function getPayloadFields(Request $request): JsonResponse
    {
        $shopDomain = $this->authService->getShopDomain();
        $event = $request->query('event');

        if (!$shopDomain || !$event) {
            return response()->json([]);
        }

        // Fetch latest payload for this account + event
        $payload = WebhookPayload::where('platform_id', $shopDomain)
            ->where('event', $event)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$payload) {
            return response()->json([]);
        }

        $data = $payload->payload;
        $fields = [];
        $this->extractPaths($data, '', $fields);

        return response()->json($fields);
    }

    /**
     * Replace placeholders like {{contact.email}} with actual values
     */
    private function replacePlaceholders(string $text, array $data): string
    {
        return preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($data) {
            $keys = explode('.', trim($matches[1]));
            $value = $data;

            foreach ($keys as $k) {
                if (is_numeric($k)) {
                    $k = (int) $k;
                }
                if (!is_array($value) || !array_key_exists($k, $value)) {
                    return '';
                }
                $value = $value[$k];
            }

            return (string) $value;
        }, $text);
    }

    /**
     * Extract all paths from nested array for field picker
     */
    private function extractPaths(array $data, string $prefix, array &$fields): void
    {
        foreach ($data as $key => $value) {
            $path = $prefix === '' ? $key : $prefix . '.' . $key;
            if (is_array($value)) {
                $this->extractPaths($value, $path, $fields);
            } else {
                $fields[$path] = $value;
            }
        }
    }
}
