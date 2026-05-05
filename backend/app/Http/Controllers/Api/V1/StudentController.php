<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Validate guardian access.
     */
    private function validateGuardian(): ?JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        return null;
    }

    /**
     * List students linked to the authenticated guardian.
     */
    public function index(): JsonResponse
    {
        if ($resp = $this->validateGuardian()) {
            return $resp;
        }

        $guardian = Auth::user()->guardian;

        $students = Student::where('guardian_id', $guardian->id)
            ->whereNull('trashed_at')
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
     */
    public function show(Student $student): JsonResponse
    {
        if ($resp = $this->validateGuardian()) {
            return $resp;
        }

        $guardian = Auth::user()->guardian;

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student');
        }

        $student->load([
            'guardian',
            'progressRecords' => fn($query) => $query->whereNull('trashed_at')->with('week'),
            'weeklySummaries' => fn($query) => $query->whereNull('trashed_at')->with('week'),
        ]);

        return $this->successResponse(
            new StudentResource($student),
            'Student details retrieved successfully'
        );
    }
}
