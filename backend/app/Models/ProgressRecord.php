<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'week_id',       // must be here
        'subject',
        'rating_level',
        'remarks',       // optional if you added this column
    ];

    /**
     * Map numeric ratings to labels.
     */
    public const RATINGS = [
        1 => 'Poor',
        2 => 'Good',
        3 => 'Very Good',
        4 => 'Excellent',
    ];

    public function getRatingLabelAttribute(): string
    {
        return self::RATINGS[$this->rating_level] ?? 'Unknown';
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
