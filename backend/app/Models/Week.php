<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_number',
        'start_date',
        'end_date',
    ];

    /**
     * A week has many progress records.
     */
    public function progressRecords()
    {
        return $this->hasMany(ProgressRecord::class, 'week_id');
    }

    /**
     * A week has many weekly summaries.
     */
    public function weeklySummaries()
    {
        return $this->hasMany(WeeklySummary::class, 'week_id');
    }
}
