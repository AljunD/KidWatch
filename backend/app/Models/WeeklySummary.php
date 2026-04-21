<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklySummary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'week_id',
        'summary_text',
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
}
