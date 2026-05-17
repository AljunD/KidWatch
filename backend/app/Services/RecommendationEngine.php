<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;

class RecommendationEngine
{
    /**
     * Generate structured recommendation activities based on subject ratings.
     *
     * @param array $ratings  Subject ratings for the current week
     * @param int   $weekId   Current week ID
     *
     * @return array
     */
    public function getActivities(array $ratings, int $weekId): array
    {
        $rules      = Config::get('recommendation_rules');
        $activities = [];

        // Rating labels for readability
        $labels = [
            0 => 'No Classes',
            1 => 'Needs Attention',
            2 => 'For Improvement',
            3 => 'Very Good',
            4 => 'Excellent',
        ];

        foreach ($ratings as $subject => $rating) {
            $subjectKey = strtolower($subject);

            // Ensure subject and rating rule exist
            if (isset($rules['weeks'][$weekId][$subjectKey]['rating_rules'][$rating])) {
                $rule = $rules['weeks'][$weekId][$subjectKey]['rating_rules'][$rating];

                $activities[$subjectKey] = [
                    'subject'      => ucfirst($subjectKey),
                    'rating'       => $labels[$rating] ?? null,
                    'activity'     => $rule['activity'] ?? null,
                    'narrative'    => $rule['narrative'] ?? null,
                    'priority'     => $rule['priority'] ?? null,
                    'guardian_tip' => $rule['guardian_tip'] ?? null,
                    'student_tip'  => $rule['student_tip'] ?? null,
                ];
            }
        }

        return $activities;
    }
}
