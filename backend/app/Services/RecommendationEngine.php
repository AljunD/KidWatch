<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;

class RecommendationEngine
{
    public function getActivities(array $ratings): array
    {
        $rules      = Config::get('recommendation_rules');
        $activities = [];

        foreach ($ratings as $subject => $rating) {
            $subjectKey = strtolower($subject);

            if (isset($rules[$subjectKey][$rating])) {
                $rule = $rules[$subjectKey][$rating];

                $activities[] = [
                    'subject'      => ucfirst($subjectKey),
                    'rating'       => $rating,
                    'activity'     => $rule['activity']     ?? '',
                    'category'     => $rule['category']     ?? 'general',
                    'priority'     => $rule['priority']     ?? 'medium',
                    'guardian_tip' => $rule['guardian_tip'] ?? null,
                    'student_tip'  => $rule['student_tip']  ?? null,
                ];
            }
        }

        if (in_array(1, $ratings, true)) {
            $activities[] = [
                'subject'      => 'General',
                'rating'       => 1,
                'activity'     => "Organize a parent-teacher conference to address persistent Needs Attention ratings.",
                'category'     => 'intervention',
                'priority'     => 'high',
                'guardian_tip' => "Coordinate with teachers to create a targeted support plan.",
                'student_tip'  => "Be open to feedback and commit to improvement strategies.",
            ];
        }

        if (!empty($ratings) && min($ratings) >= 3) {
            $activities[] = [
                'subject'      => 'General',
                'rating'       => max($ratings),
                'activity'     => "Encourage enrichment activities such as clubs, competitions, or peer tutoring.",
                'category'     => 'enrichment',
                'priority'     => 'low',
                'guardian_tip' => "Support participation in academic clubs or competitions.",
                'student_tip'  => "Challenge yourself with enrichment tasks and help peers.",
            ];
        }

        return $activities;
    }
}
