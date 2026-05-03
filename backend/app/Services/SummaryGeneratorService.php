<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\ProgressRecord;
use Illuminate\Support\Collection;

class SummaryGeneratorService
{
    public function generate(Student $student, int $weekId, ?array $previousRatings = null): array
    {
        /** @var Collection<ProgressRecord> $records */
        $records = $student->progressRecords()
            ->where('week_id', $weekId)
            ->orderBy('subject')
            ->get();

        if ($records->isEmpty()) {
            return [
                'summary'    => "No progress records available for {$student->full_name} in Week {$weekId}.",
                'activities' => []
            ];
        }

        $narrative  = "Weekly Progress Summary – Week {$weekId}\n";
        $narrative .= "Student: {$student->full_name}\n";
        $narrative .= "Generated on " . now()->format('F j, Y') . "\n\n";

        $labels = [
            0 => 'No Classes',
            1 => 'Needs Attention',
            2 => 'Good',
            3 => 'Very Good',
            4 => 'Excellent',
        ];

        // Collect ratings + remarks
        $ratings        = [];
        $remarksSummary = [];
        foreach ($records as $record) {
            $ratings[strtolower($record->subject)] = $record->rating_level;
            if ($record->remarks) {
                $remarksSummary[] = "{$record->subject}: {$record->remarks}";
            }
        }

        // Subject ratings section
        foreach ($records as $record) {
            $label = $labels[$record->rating_level] ?? (string) $record->rating_level;
            $narrative .= strtoupper($record->subject) . ": {$label}";
            if ($record->remarks) {
                $narrative .= " – {$record->remarks}";
            }
            $narrative .= ".\n";
        }

        $summaryText = "Performance Analysis:\n";

        $strengths  = array_keys(array_filter($ratings, fn($r) => $r >= 3));
        $weaknesses = array_keys(array_filter($ratings, fn($r) => $r <= 1));

        if ($strengths) {
            $summaryText .= "Strengths in " . implode(', ', $strengths) . " show mastery and readiness for advanced tasks.\n";
        }
        if ($weaknesses) {
            $summaryText .= "Weaknesses in " . implode(', ', $weaknesses) . " require closer attention and support.\n";
        }

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

        if (!empty($remarksSummary)) {
            $summaryText .= "\nRemarks Summary:\n";
            foreach ($remarksSummary as $remark) {
                $summaryText .= "- {$remark}\n";
            }
        }

        $engine     = new RecommendationEngine();
        $activities = $engine->getActivities($ratings);

        if (in_array(1, $ratings, true)) {
            $activities[] = [
                'activity'     => "Organize a parent-teacher conference to address persistent Needs Attention ratings.",
                'category'     => 'intervention',
                'priority'     => 'high',
                'guardian_tip' => "Schedule a meeting with teachers to discuss targeted support.",
                'student_tip'  => "Be open to feedback and commit to improvement plans.",
            ];
        }

        $narrative .= "\nSummary:\n{$summaryText}\n";
        $narrative .= "End of summary.";

        return [
            'summary'    => $narrative,
            'activities' => $activities
        ];
    }
}
