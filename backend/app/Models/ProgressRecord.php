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
        'week_id',       // must be here
        'subject',
        'rating_level',
        'remarks',       // optional if you added this column
        'trashed_at',
        'deleted_at',
    ];

    protected $dates = [
        'trashed_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Map numeric ratings to labels.
     */
    public const RATINGS = [
        0 => 'No Classes',
        1 => 'Poor',
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
