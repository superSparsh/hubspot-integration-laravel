<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HubSpot API Configuration
    |--------------------------------------------------------------------------
    */

    'client_id' => env('HUBSPOT_CLIENT_ID'),
    'client_secret' => env('HUBSPOT_CLIENT_SECRET'),
    'redirect_uri' => env('HUBSPOT_REDIRECT_URI', 'https://hubspot.tittu.in/integrations/hubspot/callback'),
    'scopes' => env('HUBSPOT_SCOPES', 'oauth,crm.objects.contacts.read,crm.objects.deals.read'),
    'api_base' => env('HUBSPOT_API_BASE', 'https://api.hubapi.com'),
    'webhook_secret' => env('HUBSPOT_WEBHOOK_SECRET'),
];
