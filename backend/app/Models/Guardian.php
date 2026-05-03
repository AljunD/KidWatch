<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'relationship_to_child',
        'contact_number',
        'address',
        'trashed_at',
    ];

    protected $casts = [
        'trashed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function trash(): void
    {
        $this->trashed_at = now();
        $this->save();

        if ($this->user) {
            $this->user->email = "trashed_guardian_{$this->id}@example.com";
            $this->user->save();
        }
    }

    public function restoreFromTrash(): void
    {
        $this->trashed_at = null;
        $this->save();
    }

    public function hardDelete(): void
    {
        if ($this->user) {
            $this->user->delete();
        }

        $this->delete();
    }
}
