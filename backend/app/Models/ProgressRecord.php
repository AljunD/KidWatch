<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week_id',
        'subject',
        'rating_level',
        'remarks',
        'trashed_at',
    ];

    protected $casts = [
        'trashed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const RATINGS = [
        0 => 'No Classes',
        1 => 'Needs Attention',
        2 => 'Good',
        3 => 'Very Good',
        4 => 'Excellent',
    ];

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getRatingLabelAttribute(): string
    {
        return self::RATINGS[$this->rating_level] ?? 'Unknown';
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
        $this->delete();
    }
}
