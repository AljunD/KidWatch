<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;

class RecommendationEngine
{
    /**
     * Generate structured recommendation activities based on subject ratings.
     *
     * Each activity includes:
     * - activity (string)
     * - category (string)
     * - priority (string: high/medium/low)
     * - guardian_tip (string)
     * - student_tip (string)
     */
    public function getActivities(array $ratings): array
    {
        $rules = Config::get('recommendation_rules');
        $activities = [];

        foreach ($ratings as $subject => $rating) {
            $subjectKey = strtolower($subject);

            if (isset($rules[$subjectKey][$rating])) {
                // Return the full structured rule object
                $activities[] = [
                    'subject'      => ucfirst($subjectKey),
                    'rating'       => $rating,
                    'activity'     => $rules[$subjectKey][$rating]['activity'] ?? '',
                    'category'     => $rules[$subjectKey][$rating]['category'] ?? 'general',
                    'priority'     => $rules[$subjectKey][$rating]['priority'] ?? 'medium',
                    'guardian_tip' => $rules[$subjectKey][$rating]['guardian_tip'] ?? null,
                    'student_tip'  => $rules[$subjectKey][$rating]['student_tip'] ?? null,
                ];
            }
        }

        // Extra global rule: parent-teacher conference if any Poor ratings persist
        if (in_array(1, $ratings, true)) {
            $activities[] = [
                'subject'      => 'General',
                'rating'       => 1,
                'activity'     => "Organize a parent-teacher conference if any Poor ratings persist.",
                'category'     => 'intervention',
                'priority'     => 'high',
                'guardian_tip' => "Coordinate with teachers to create a support plan.",
                'student_tip'  => "Be open to feedback and commit to improvement strategies.",
            ];
        }

        return $activities;
    }
}
