<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trigger extends Model
{
    protected $fillable = [
        'uuid',
        'account_id',
        'event',
        'trigger_name',
        'template_uid',
        'template_name',
        'to_field',
        'api_token',
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
