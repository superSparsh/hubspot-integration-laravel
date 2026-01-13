<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookPayload extends Model
{
    protected $fillable = [
        'platform_id',
        'event',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
