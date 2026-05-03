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

        $students = Student::where('guardian_id', $guardian->id)
            ->whereNull('trashed_at') // skip trashed students
            ->paginate(10);

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
        // Ensure guardian can only access their own student
        $guardian = Auth::user()->guardian;
        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->errorResponse('Unauthorized', 403, ['You cannot access this student']);
        }

        $student->load(['guardian', 'progressRecords' => function ($query) {
            $query->whereNull('trashed_at');
        }]);

        return $this->successResponse(
            new StudentResource($student),
            'Student details retrieved successfully'
        );
    }
}
