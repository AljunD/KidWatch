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

    /**
     * Accessor for the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Accessor for student age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Relationship: A student belongs to one guardian.
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    /**
     * Relationship: All historical progress records.
     */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class);
    }

    /**
     * Relationship for the Dashboard Monitor.
     * Fetches only the records for the current academic week.
     */
    public function latest_progress(): HasMany
    {
        return $this->hasMany(ProgressRecord::class)
            ->where('week_number', Carbon::now()->weekOfYear);
    }

    /**
     * Relationship: All historical weekly summaries.
     */
    public function weeklySummaries(): HasMany
    {
        return $this->hasMany(WeeklySummary::class);
    }

    /**
     * Relationship for the Dashboard Monitor.
     * Fetches the specific narrative summary for the current week.
     */
    public function weekly_summary(): HasOne
    {
        return $this->hasOne(WeeklySummary::class)
            ->where('week_number', Carbon::now()->weekOfYear);
    }

    /**
     * Soft delete (move to trash).
     */
    public function trash(): void
    {
        $this->trashed_at = now();
        $this->save();
    }

    /**
     * Restore from trash.
     */
    public function restoreFromTrash(): void
    {
        $this->trashed_at = null;
        $this->save();
    }

    /**
     * Hard delete (permanent removal).
     */
    public function hardDelete(): void
    {
        $this->deleted_at = now();
        $this->save();
        parent::delete(); // permanently remove from DB
    }
}
