<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    protected $fillable = [
        'trigger_id',
        'recipient',
        'response_code',
        'response_body',
    ];

    public function trigger()
    {
        return $this->belongsTo(Trigger::class);
    }
}
