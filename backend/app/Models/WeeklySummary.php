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
        'week_number',
        'summary_text',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
