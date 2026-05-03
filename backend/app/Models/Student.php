<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'nationality',
        'religion',
        'photo_path',
        'trashed_at',
        'deleted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'trashed_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class);
    }

    public function latest_progress(): HasMany
    {
        return $this->hasMany(ProgressRecord::class)
            ->where('week_number', Carbon::now()->weekOfYear);
    }

    public function weeklySummaries(): HasMany
    {
        return $this->hasMany(WeeklySummary::class);
    }

    public function weekly_summary(): HasOne
    {
        return $this->hasOne(WeeklySummary::class)
            ->where('week_number', Carbon::now()->weekOfYear);
    }

    public function trash(): void
    {
        $this->trashed_at = now();
        $this->save();
    }

    public function restoreFromTrash(): void
    {
        $this->trashed_at = null;
        $this->save();
    }

    public function hardDelete(): void
    {
        $this->deleted_at = now();
        $this->save();
        parent::delete();
    }
}
