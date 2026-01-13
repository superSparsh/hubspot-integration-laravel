<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriggerVariable extends Model
{
    protected $fillable = [
        'trigger_id',
        'var_key',
        'var_path',
    ];

    public function trigger()
    {
        return $this->belongsTo(Trigger::class);
    }
}
