<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecommendationEngineConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recommendation_engine_configs';

    protected $fillable = [
        'subject',
        'rating_level', // Updated column name
        'intervention_text',
    ];

    public function getRatingLabelAttribute(): string
    {
        return ProgressRecord::RATINGS[$this->rating_level] ?? 'Unknown';
    }
}
