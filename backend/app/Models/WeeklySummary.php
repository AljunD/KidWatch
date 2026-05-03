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
        'activities_text',
        'trashed_at',
    ];

    protected $casts = [
        'trashed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'week_id');
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
