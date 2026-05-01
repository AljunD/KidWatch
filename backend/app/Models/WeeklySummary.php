<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week_id',
        'summary_text',
        'trashed_at',
        'deleted_at',
    ];

    protected $casts = [
        'trashed_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * A weekly summary belongs to a student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * A weekly summary belongs to a week.
     */
    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'week_id');
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
