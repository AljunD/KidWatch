<?php

namespace App\Services;

use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    /**
     * Record weekly progress and summary for a preschool student.
     *
     * ECCD-aligned logic:
     * - Ratings (0–4) are all valid, with 0 meaning "No Classes"
     * - Summary text should be narrative and child-friendly
     * - Activities should be play-based, structured with guardian and student tips
     *
     * @param int    $studentId   The student ID
     * @param int    $weekId      The week ID
     * @param array  $ratings     Subject => rating_level (0–4)
     * @param string $summaryText Narrative summary text (ECCD-aligned)
     * @param array  $activities  Structured recommendation activities (play-based)
     *
     * @return WeeklySummary
     */
    public function recordWeeklyProgress(
        int $studentId,
        int $weekId,
        array $ratings,
        string $summaryText,
        array $activities
    ) {
        return DB::transaction(function () use ($studentId, $weekId, $ratings, $summaryText, $activities) {

            // Step 1: Sync Ratings (store/update each subject rating)
            foreach ($ratings as $subject => $rating) {
                ProgressRecord::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'week_id'    => $weekId,
                        'subject'    => $subject,
                    ],
                    [
                        'rating_level' => $rating, // 0–4 valid, 0 = No Classes
                    ]
                );
            }

            // Step 2: Store summary and activities
            // Activities are JSON encoded for structured retrieval in ECCD format
            return WeeklySummary::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'week_id'    => $weekId,
                ],
                [
                    // Narrative summary should use preschool-friendly language
                    // Example: "This week, Maria enjoyed singing songs in English and showed steady progress in counting blocks."
                    'summary_text'    => $summaryText,

                    // Activities include subject, rating, activity, priority, guardian_tip, student_tip
                    // Example activity: "Play counting games with toys"
                    'activities_text' => json_encode(
                        $activities,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                    ),
                ]
            );
        });
    }
}
