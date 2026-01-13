<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubSpotWebhookLog extends Model
{
    protected $fillable = [
        'hubspot_portal_id',
        'event_type',
        'payload',
        'signature',
        'verified',
        'status',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
        'verified' => 'boolean',
    ];
}
