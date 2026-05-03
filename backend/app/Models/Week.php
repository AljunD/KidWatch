<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Week extends Model
{
    use HasFactory;

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

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class, 'week_id');
    }

    public function weeklySummaries(): HasMany
    {
        return $this->hasMany(WeeklySummary::class, 'week_id');
    }
}
