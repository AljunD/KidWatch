<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\WeeklySummaryResource;
use App\Http\Resources\ProgressResource;
use App\Models\Student;
use App\Models\WeeklySummary;
use App\Models\Week;
use App\Models\ProgressRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    /**
     * Validate guardian and student ownership.
     */
    private function validateGuardianAccess(Student $student): ?JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student');
        }

        return null;
    }

    /**
     * Build week metadata for frontend uniformity.
     * ✅ Format dates to show only YYYY-MM-DD
     */
    private function buildWeekInfo(Week $week, int $summaryCount = 0): array
    {
        return [
            'week_number'     => $week->week_number,
            'start_date'      => $week->start_date ? $week->start_date->format('Y-m-d') : null,
            'end_date'        => $week->end_date ? $week->end_date->format('Y-m-d') : null,
            'summaries_count' => $summaryCount,
        ];
    }

    /**
     * Show a weekly summary for a specific student and week.
     */
    public function show(Student $student, int $weekId): JsonResponse
    {
        if ($resp = $this->validateGuardianAccess($student)) {
            return $resp;
        }

        $summary = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->with('week')
            ->first();

        $progress = $student->progressRecords()
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->with('week')
            ->get();

        // Ensure fixed subject order
        $subjectOrder = ['Math', 'Science', 'English', 'Filipino'];
        $progress = $progress->sortBy(fn($record) => array_search($record->subject, $subjectOrder))->values();

        // Add placeholder if latest week has no records
        $latestWeek = Week::orderBy('week_number', 'desc')->first();
        if ($latestWeek && $latestWeek->id == $weekId && $progress->isEmpty()) {
            $placeholder = new ProgressRecord([
                'student_id'   => $student->id,
                'week_id'      => $latestWeek->id,
                'subject'      => null,
                'rating_level' => null,
                'remarks'      => null,
            ]);
            $placeholder->setRelation('week', $latestWeek);
            $progress->prepend($placeholder);
        }

        return $this->successResponse([
            'summary'           => $summary ? new WeeklySummaryResource($summary) : null,
            'progress_records'  => ProgressResource::collection($progress),
            'can_generate'      => $progress->count() >= 4,
            'summary_generated' => $summary !== null,
            'week_info'         => $summary
                ? $this->buildWeekInfo($summary->week, 1)
                : ($latestWeek ? $this->buildWeekInfo($latestWeek, 0) : null),
        ], $summary ? 'Weekly summary retrieved successfully' : 'No weekly summary available');
    }

    /**
     * List all weekly summaries for a specific student.
     */
    public function index(Student $student): JsonResponse
    {
        if ($resp = $this->validateGuardianAccess($student)) {
            return $resp;
        }

        $summaries = WeeklySummary::where('student_id', $student->id)
            ->whereNull('trashed_at')
            ->with('week')
            ->orderByDesc('week_id')
            ->paginate(10);

        $meta = [
            'pagination' => [
                'current_page' => $summaries->currentPage(),
                'last_page'    => $summaries->lastPage(),
                'per_page'     => $summaries->perPage(),
                'total'        => $summaries->total(),
            ]
        ];

        return $this->successResponse(
            WeeklySummaryResource::collection($summaries),
            $summaries->isEmpty() ? 'No weekly summaries available' : 'Weekly summaries retrieved successfully',
            200,
            $meta
        );
    }

    /**
     * Generate or regenerate a weekly summary for a student and week.
     */
    public function generate(Student $student, int $weekId, Request $request): JsonResponse
    {
        if ($resp = $this->validateGuardianAccess($student)) {
            return $resp;
        }

        $summary = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->first();

        if ($summary) {
            $summary->update([
                'summary_text'    => "Regenerated summary for week {$weekId} based on student progress.",
                'activities_text' => "Updated recommended activities.",
            ]);
            $summary->load('week');

            return $this->successResponse(
                new WeeklySummaryResource($summary),
                'Weekly summary regenerated successfully',
                200,
                ['week_info' => $this->buildWeekInfo($summary->week, 1)]
            );
        }

        $summary = WeeklySummary::create([
            'student_id'      => $student->id,
            'week_id'         => $weekId,
            'summary_text'    => "Auto-generated summary for week {$weekId} based on student progress.",
            'activities_text' => "Recommended activities will be generated here.",
        ]);
        $summary->load('week');

        return $this->successResponse(
            new WeeklySummaryResource($summary),
            'Weekly summary generated successfully',
            201,
            ['week_info' => $this->buildWeekInfo($summary->week, 1)]
        );
    }
}
