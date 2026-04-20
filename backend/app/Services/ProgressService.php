<?php

namespace App\Services;

use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgressService
{
    /**
     * Handles the atomic persistence of progress and summaries.
     */
    public function recordWeeklyProgress(int $studentId, array $ratings, string $summaryText)
    {
        $weekNumber = Carbon::now()->weekOfYear;
        $teacherId = auth()->id();

        return DB::transaction(function () use ($studentId, $ratings, $summaryText, $weekNumber, $teacherId) {

            // Sync Ratings
            foreach ($ratings as $subject => $rating) {
                ProgressRecord::updateOrCreate(
                    ['student_id' => $studentId, 'week_number' => $weekNumber, 'subject' => $subject],
                    ['rating_level' => $rating, 'teacher_id' => $teacherId]
                );
            }

            // Sync Summary
            return WeeklySummary::updateOrCreate(
                ['student_id' => $studentId, 'week_number' => $weekNumber],
                ['summary_text' => $summaryText, 'teacher_id' => $teacherId]
            );
        });
    }
}
