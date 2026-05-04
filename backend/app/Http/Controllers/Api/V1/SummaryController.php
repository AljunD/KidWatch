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
    public function show(Student $student, int $weekId): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student\'s summary');
        }

        $summary = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->with('week')
            ->first();

        if (!$summary) {
            // ✅ Return 200 OK with empty array instead of 404
            return $this->successResponse([], 'No weekly summary available');
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
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student\'s summaries');
        }

        $summaries = WeeklySummary::where('student_id', $student->id)
            ->whereNull('trashed_at')
            ->with('week')
            ->paginate(10);

        $meta = [
            'pagination' => [
                'current_page' => $summaries->currentPage(),
                'last_page'    => $summaries->lastPage(),
                'per_page'     => $summaries->perPage(),
                'total'        => $summaries->total(),
            ]
        ];

        if ($summaries->isEmpty()) {
            // ✅ Return 200 OK with empty array
            return $this->successResponse([], 'No weekly summaries available', 200, $meta);
        }

        return $this->successResponse(
            WeeklySummaryResource::collection($summaries),
            'Weekly summaries retrieved successfully',
            200,
            $meta
        );
    }

    /**
     * Generate a weekly summary for a student and week.
     */
    public function generate(Student $student, int $weekId, Request $request): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null || $student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot generate a summary for this student');
        }

        $existing = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->first();

        if ($existing) {
            return $this->errorResponse('Conflict', 409, ['Summary already exists for this week']);
        }

        $summaryText = "Auto-generated summary for week {$weekId} based on student progress.";
        $activitiesText = "Recommended activities will be generated here.";

        $summary = WeeklySummary::create([
            'student_id'      => $student->id,
            'week_id'         => $weekId,
            'summary_text'    => $summaryText,
            'activities_text' => $activitiesText,
        ]);

        $summary->load('week');

        return $this->successResponse(
            new WeeklySummaryResource($summary),
            'Weekly summary generated successfully'
        );
    }
}
