<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\WeeklySummaryResource;
use App\Models\Student;
use App\Models\WeeklySummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Summary Controller
 *
 * Handles retrieval and generation of weekly summaries for a guardian's students.
 */
class SummaryController extends Controller
{
    /**
     * Show a weekly summary for a specific student and week.
     */
    public function show(Student $student, int $week): JsonResponse
    {
        $summary = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $week)
            ->whereNull('trashed_at')
            ->first();

        if (!$summary) {
            return $this->errorResponse(
                'Summary not found',
                404,
                ['No summary available for this week']
            );
        }

        return $this->successResponse(
            new WeeklySummaryResource($summary),
            'Weekly summary retrieved successfully'
        );
    }

    /**
     * List all weekly summaries for a specific student.
     */
    public function index(Student $student): JsonResponse
    {
        $summaries = WeeklySummary::where('student_id', $student->id)
            ->whereNull('trashed_at')
            ->paginate(10);

        return $this->successResponse(
            WeeklySummaryResource::collection($summaries),
            'Weekly summaries retrieved successfully',
            200,
            [
                'pagination' => [
                    'current_page' => $summaries->currentPage(),
                    'last_page'    => $summaries->lastPage(),
                    'per_page'     => $summaries->perPage(),
                    'total'        => $summaries->total(),
                ]
            ]
        );
    }

    /**
     * Generate a weekly summary for a student and week.
     */
    public function generate(Student $student, int $week, Request $request): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        // Ensure guardian owns this student
        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->errorResponse('Unauthorized', 403, ['You cannot generate a summary for this student']);
        }

        // Check if summary already exists
        $existing = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $week)
            ->whereNull('trashed_at')
            ->first();

        if ($existing) {
            return $this->errorResponse('Conflict', 409, ['Summary already exists for this week']);
        }

        // Basic generation logic (replace with your AI/Rule-based engine)
        $summaryText = "Auto-generated summary for week {$week} based on student progress.";
        $activitiesText = "Recommended activities will be generated here.";

        $summary = WeeklySummary::create([
            'student_id'     => $student->id,
            'week_id'        => $week,
            'summary_text'   => $summaryText,
            'activities_text'=> $activitiesText,
        ]);

        return $this->successResponse(
            new WeeklySummaryResource($summary),
            'Weekly summary generated successfully'
        );
    }
}
