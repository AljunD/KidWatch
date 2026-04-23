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
        'math_rating',
        'science_rating',
        'english_rating',
        'filipino_rating',
        'intervention_text',
    ];

    /**
     * Map numeric ratings to labels for readability.
     */
    public const RATINGS = [
        0 => 'No Classes',
        1 => 'Poor',
        2 => 'Good',
        3 => 'Very Good',
        4 => 'Excellent',
    ];

    /**
     * Helper to get human-readable labels for each subject rating.
     */
    public function getMathLabelAttribute(): string
    {
        return self::RATINGS[$this->math_rating] ?? 'Unknown';
    }

    public function getScienceLabelAttribute(): string
    {
        return self::RATINGS[$this->science_rating] ?? 'Unknown';
    }

    public function getEnglishLabelAttribute(): string
    {
        return self::RATINGS[$this->english_rating] ?? 'Unknown';
    }

    public function getFilipinoLabelAttribute(): string
    {
        return self::RATINGS[$this->filipino_rating] ?? 'Unknown';
    }
}
