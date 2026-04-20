<?php

// =============================================
// D. Production API Logic – SummaryGeneratorService
// app/Services/SummaryGeneratorService.php
// Deterministic Rule-Based Expert System: stitches weekly records into professional narrative using KB as single source of truth.
// Fully typed, ready for dependency injection / Laravel container.
// =============================================

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\RecommendationEngineConfig;
use Illuminate\Support\Collection;

class SummaryGeneratorService
{
    /**
     * Generate professional weekly narrative summary using the Expert System KB.
     * Called from controllers or queued jobs – deterministic and auditable.
     */
    public function generate(Student $student, int $weekNumber): string
    {
        /** @var Collection<ProgressRecord> $records */
        $records = $student->progressRecords()
            ->where('week_number', $weekNumber)
            ->orderBy('subject')
            ->get();

        if ($records->isEmpty()) {
            return "No progress records available for {$student->first_name} {$student->last_name} in Week {$weekNumber}.";
        }

        $narrative = "Weekly Progress Summary – Week {$weekNumber}\n";
        $narrative .= "Student: {$student->first_name} {$student->middle_name} {$student->last_name} (Age: {$student->age})\n";
        $narrative .= "Generated on " . now()->format('F j, Y') . "\n\n";

        foreach ($records as $record) {
            /** @var RecommendationEngineConfig|null $config */
            $config = RecommendationEngineConfig::where('subject', $record->subject)
                ->where('rating', $record->rating)
                ->first();

            $advice = $config?->intervention_text ?? 'No specific recommendation configured at this time.';

            $narrative .= strtoupper($record->subject) . ": Rated {$record->rating}.\n";
            $narrative .= "{$advice}\n\n";
        }

        $narrative .= "End of summary. Please schedule a parent-teacher conference if any Poor ratings appear.";

        return $narrative;
    }
}
