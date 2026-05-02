<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\ProgressRecord;
use Illuminate\Support\Collection;

class SummaryGeneratorService
{
    /**
     * Generate professional weekly narrative summary and recommendation activities.
     * Returns an array with 'summary' and 'activities' keys.
     */
    public function generate(Student $student, int $weekId, ?array $previousRatings = null): array
    {
        /** @var Collection<ProgressRecord> $records */
        $records = $student->progressRecords()
            ->where('week_id', $weekId)
            ->orderBy('subject')
            ->get();

        if ($records->isEmpty()) {
            return [
                'summary'    => "No progress records available for {$student->first_name} {$student->last_name} in Week {$weekId}.",
                'activities' => []
            ];
        }

        // Header
        $narrative  = "Weekly Progress Summary – Week {$weekId}\n";
        $narrative .= "Student: {$student->first_name} {$student->last_name}\n";
        $narrative .= "Generated on " . now()->format('F j, Y') . "\n\n";

        // Rating labels
        $labels = [
            0 => 'No Classes',
            1 => 'Poor',
            2 => 'Good',
            3 => 'Very Good',
            4 => 'Excellent',
        ];

        // Collect ratings + remarks
        $ratings = [];
        $remarksSummary = [];
        foreach ($records as $record) {
            $ratings[strtolower($record->subject)] = $record->rating_level;
            if ($record->remarks) {
                $remarksSummary[] = "{$record->subject}: {$record->remarks}";
            }
        }

        // Subject ratings section
        foreach ($records as $record) {
            $label = $labels[$record->rating_level] ?? $record->rating_level;
            $narrative .= strtoupper($record->subject) . ": {$label}";
            if ($record->remarks) {
                $narrative .= " – {$record->remarks}";
            }
            $narrative .= ".\n";
        }

        // Performance analysis
        $summaryText = "Performance Analysis:\n";

        $strengths  = array_keys(array_filter($ratings, fn($r) => $r >= 3));
        $weaknesses = array_keys(array_filter($ratings, fn($r) => $r <= 1));

        if ($strengths) {
            $summaryText .= "Strengths in " . implode(', ', $strengths) . " show mastery and readiness for advanced tasks.\n";
        }
        if ($weaknesses) {
            $summaryText .= "Weaknesses in " . implode(', ', $weaknesses) . " require closer attention and support.\n";
        }

        // Week-to-week comparison
        if ($previousRatings) {
            $summaryText .= "\nWeek-to-Week Comparison:\n";
            foreach ($ratings as $subject => $rating) {
                if (isset($previousRatings[$subject])) {
                    $prev = $previousRatings[$subject];
                    if ($rating > $prev) {
                        $summaryText .= ucfirst($subject) . " improved from {$labels[$prev]} to {$labels[$rating]}.\n";
                    } elseif ($rating < $prev) {
                        $summaryText .= ucfirst($subject) . " declined from {$labels[$prev]} to {$labels[$rating]}.\n";
                    } else {
                        $summaryText .= ucfirst($subject) . " remained steady at {$labels[$rating]}.\n";
                    }
                }
            }
        }

        // Remarks summary
        if (!empty($remarksSummary)) {
            $summaryText .= "\nRemarks Summary:\n";
            foreach ($remarksSummary as $remark) {
                $summaryText .= "- {$remark}\n";
            }
        }

        // Build recommendation activities via engine
        $engine = new RecommendationEngine();
        $activities = $engine->getActivities($ratings);

        // Always include parent-teacher conference rule
        if (in_array(1, $ratings, true)) {
            $activities[] = "Organize a parent-teacher conference if any Poor ratings persist.";
        }

        // Final assembly
        $narrative .= "\nSummary:\n{$summaryText}\n";
        $narrative .= "End of summary.";

        return [
            'summary'    => $narrative,
            'activities' => $activities
        ];
    }
}
