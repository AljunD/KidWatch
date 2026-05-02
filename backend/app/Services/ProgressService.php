<?php

namespace App\Services;

use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    /**
     * Handles the atomic persistence of progress records and weekly summaries.
     */
    public function recordWeeklyProgress(int $studentId, int $weekId, array $ratings, string $summaryText, array $activities)
    {
        return DB::transaction(function () use ($studentId, $weekId, $ratings, $summaryText, $activities) {

            // Sync Ratings
            foreach ($ratings as $subject => $rating) {
                ProgressRecord::updateOrCreate(
                    ['student_id' => $studentId, 'week_id' => $weekId, 'subject' => $subject],
                    ['rating_level' => $rating]
                );
            }

            // Sync Summary + Activities
            return WeeklySummary::updateOrCreate(
                ['student_id' => $studentId, 'week_id' => $weekId],
                [
                    'summary_text'    => $summaryText,
                    'activities_text' => implode("\n", $activities)
                ]
            );
        });
    }
}
