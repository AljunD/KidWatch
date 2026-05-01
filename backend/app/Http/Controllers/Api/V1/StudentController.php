<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        // Get the currently authenticated guardian
        $guardian = Auth::user()->guardian;

        // Only fetch students linked to this guardian
        $students = Student::where('guardian_id', $guardian->id)
            ->paginate(10);

        return $this->successResponse(
            StudentResource::collection($students),
            'Students retrieved successfully'
        );
    }

    public function show(Student $student): JsonResponse
    {
        $student->load('guardian', 'progressRecords');
        return $this->successResponse(new StudentResource($student), 'Student details retrieved successfully');
    }
}
