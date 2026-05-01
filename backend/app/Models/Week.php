<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Week extends Model
{
    use HasFactory;

    // ✅ Disable timestamps because weeks table has no created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'week_number',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * A week has many progress records.
     */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class, 'week_id');
    }

    /**
     * A week has many weekly summaries.
     */
    public function weeklySummaries(): HasMany
    {
        return $this->hasMany(WeeklySummary::class, 'week_id');
    }
}
