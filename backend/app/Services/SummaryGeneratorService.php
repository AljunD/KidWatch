<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\ProgressRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

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

        // Collect ratings
        $ratings = [];
        foreach ($records as $record) {
            $ratings[strtolower($record->subject)] = $record->rating_level;
        }

        // Categorize
        $strengths    = array_keys(array_filter($ratings, fn($r) => $r >= 3));
        $improvements = array_keys(array_filter($ratings, fn($r) => $r === 2));
        $weaknesses   = array_keys(array_filter($ratings, fn($r) => $r === 1));

        $rules = Config::get('recommendation_rules');
        $engine = new RecommendationEngine();
        $allActivities = $engine->getActivities($ratings, $weekId);

        $name = $student->first_name;

// =========================
// FRIENDLY PARAGRAPH OUTPUT (FIXED)
// =========================

$narrative = "Week {$weekId} Progress Summary for {$student->full_name}\n\n";

// Paragraph 1: Overview
$narrative .= "This week, {$name} participated in different learning activities designed to support growth in various areas. ";

if (!empty($strengths)) {
    $narrative .= "{$name} showed strong performance in " . implode(', ', $strengths) . ". ";
}

if (!empty($improvements)) {
    $narrative .= "{$name} is also showing steady progress in " . implode(', ', $improvements) . ". ";
}

if (!empty($weaknesses)) {
    $narrative .= "Some areas like " . implode(', ', $weaknesses) . " may need a little more guidance and practice.";
} else {
    $narrative .= "There are no major areas of concern this week.";
}

$narrative .= "\n\n";

// Paragraph 2: Activities (FIX punctuation)
$activityParts = [];

foreach ($ratings as $subject => $rating) {
    $weekly = $rules['weeks'][$weekId][$subject]['weekly_activity']['activity'] ?? null;

    if ($weekly) {
        $clean = rtrim(strtolower($weekly), '.'); // remove trailing dot
        $activityParts[] = $clean;
    }
}

if (!empty($activityParts)) {
    $narrative .= "During the week, {$name} engaged in activities such as "
        . implode(', ', $activityParts)
        . ". These activities help build confidence, curiosity, and essential learning skills.\n\n";
}

// Paragraph 3: Recommendations (FIX STRUCTURE)
$narrative .= "To continue supporting {$name}'s development, here are some simple suggestions:\n\n";

foreach ($ratings as $subject => $rating) {
    $recommendation = $allActivities[$subject] ?? null;
    $ruleNarrative = $rules['weeks'][$weekId][$subject]['rating_rules'][$rating]['narrative'] ?? null;

    if ($recommendation && $ruleNarrative) {

        $activityText = rtrim($recommendation['activity'], '.');
        $guardianTip = lcfirst($recommendation['guardian_tip']);
        $studentTip = lcfirst($recommendation['student_tip']);

        $narrative .= ucfirst($subject) . ":\n";
        $narrative .= "{$ruleNarrative}\n";
        $narrative .= "Try this activity at home: {$activityText}.\n";
        $narrative .= "Guardian Tip: {$guardianTip}.\n";
        $narrative .= "Student Tip: {$studentTip}.\n\n";
    }
}

// Paragraph 4: Closing
$narrative .= "Overall, {$name} is making meaningful progress. By continuing to celebrate achievements, supporting areas for improvement, and engaging in fun learning activities, {$name} will keep building a strong foundation for future learning.";
        // =========================
        // STRUCTURED ACTIVITIES (UNCHANGED LOGIC)
        // =========================

        $structuredActivities = [
            'strengths'    => [],
            'improvements' => [],
            'weaknesses'   => [],
        ];

        foreach ($strengths as $subject) {
            $structuredActivities['strengths'][$subject] = $this->buildActivity($rules, $allActivities, $ratings, $weekId, $subject);
        }

        foreach ($improvements as $subject) {
            $structuredActivities['improvements'][$subject] = $this->buildActivity($rules, $allActivities, $ratings, $weekId, $subject);
        }

        foreach ($weaknesses as $subject) {
            $structuredActivities['weaknesses'][$subject] = $this->buildActivity($rules, $allActivities, $ratings, $weekId, $subject);
        }

        return [
            'summary'    => $narrative,
            'activities' => $structuredActivities,
        ];
    }

    private function buildActivity(array $rules, array $allActivities, array $ratings, int $weekId, string $subject): array
    {
        return [
            'weekly_activity' => $rules['weeks'][$weekId][$subject]['weekly_activity']['activity'] ?? null,
            'recommendation'  => $allActivities[$subject]['activity'] ?? null,
            'narrative'       => $rules['weeks'][$weekId][$subject]['rating_rules'][$ratings[$subject]]['narrative'] ?? null,
            'guardian_tip'    => $allActivities[$subject]['guardian_tip'] ?? null,
            'student_tip'     => $allActivities[$subject]['student_tip'] ?? null,
        ];
    }
}
