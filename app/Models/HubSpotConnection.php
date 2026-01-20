<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HubSpotConnection extends Model
{
    use HasUuids;

    protected $table = 'hubspot_connections';

    protected $fillable = [
        'wapapp_account_id',
        'hubspot_portal_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];
}
