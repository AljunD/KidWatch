<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return ucfirst($this->action);
    }

    public function getFormattedDateAttribute(): ?string
    {
        return $this->created_at?->format('M d, Y h:i A');
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('Logs are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Logs are immutable and cannot be deleted.');
        });
    }
}
