<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute()
    {
        return ucfirst($this->action);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y h:i A') : null;
    }
}
