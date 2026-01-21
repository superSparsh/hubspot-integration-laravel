<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trigger extends Model
{
    protected $fillable = [
        'uuid',
        'account_id',
        'shop_domain',
        'event',
        'trigger_name',
        'template_uid',
        'template_name',
        'to_field',
        'api_token',
        'integration_type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($trigger) {
            if (empty($trigger->uuid)) {
                $trigger->uuid = (string) Str::uuid();
            }
        });
    }

    public function variables()
    {
        return $this->hasMany(TriggerVariable::class);
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }
}
