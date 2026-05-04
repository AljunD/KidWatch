<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Student Controller
 *
 * Handles retrieval of students linked to the authenticated guardian.
 */
class StudentController extends Controller
{
    /**
     * List students linked to the authenticated guardian.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        // ✅ Guardian must exist and not be trashed
        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        $students = Student::where('guardian_id', $guardian->id)
            ->whereNull('trashed_at') // skip trashed students
            ->paginate(10);

        if ($students->isEmpty()) {
            return $this->notFoundResponse('Students');
        }

        return $this->successResponse(
            StudentResource::collection($students),
            'Students retrieved successfully',
            200,
            [
                'pagination' => [
                    'current_page' => $students->currentPage(),
                    'last_page'    => $students->lastPage(),
                    'per_page'     => $students->perPage(),
                    'total'        => $students->total(),
                ]
            ]
        );
    }

    /**
     * Show details for a specific student.
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function show(Student $student): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        // ✅ Guardian must exist and not be trashed
        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        // ✅ Ensure guardian owns this student and student is active
        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student');
        }

        // ✅ Load guardian + active progress records + weekly summaries
        $student->load([
            'guardian',
            'progressRecords' => function ($query) {
                $query->whereNull('trashed_at')->with('week');
            },
            'weeklySummaries' => function ($query) {
                $query->whereNull('trashed_at')->with('week');
            }
        ]);

        return $this->successResponse(
            new StudentResource($student),
            'Student details retrieved successfully'
        );
    }
}
