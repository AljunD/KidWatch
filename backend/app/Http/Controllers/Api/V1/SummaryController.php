<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\WeeklySummaryResource;
use App\Models\Student;
use App\Models\WeeklySummary;
use Illuminate\Http\JsonResponse;

class SummaryController extends Controller
{
    public function show(Student $student, int $week): JsonResponse
    {
        $summary = WeeklySummary::where('student_id', $student->id)
            ->where('week_id', $week)
            ->first();

        if (!$summary) {
            return $this->errorResponse('Summary not found', 404, ['No summary available for this week']);
        }

        return $this->successResponse(new WeeklySummaryResource($summary), 'Weekly summary retrieved successfully');
    }
}
