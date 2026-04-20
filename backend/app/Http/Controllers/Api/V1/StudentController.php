<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Teachers can list all students.
        // Guardians will only see their own linked students (handled by check.student.access middleware).
        $students = Student::with('guardians')->paginate(15);
        return $this->successResponse(StudentResource::collection($students));
    }

    /**
     * Only teacher/admin can add new students (with guardian info inside the form).
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, ['admin', 'teacher'])) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $student = Student::create($request->validated());
        return $this->successResponse(new StudentResource($student), 'Student created', 201);
    }

    public function show(Student $student): JsonResponse
    {
        // Guardians can only view their own student (enforced by middleware).
        $student->load('guardians', 'progressRecords');
        return $this->successResponse(new StudentResource($student));
    }

    /**
     * Only teacher/admin can update student details.
     */
    public function update(StoreStudentRequest $request, Student $student): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, ['admin', 'teacher'])) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $student->update($request->validated());
        return $this->successResponse(new StudentResource($student), 'Student updated');
    }

    /**
     * Soft delete (move to trash).
     * Restricted to teacher/admin only.
     */
    public function destroy(Student $student): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, ['admin', 'teacher'])) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $student->delete();
        return $this->successResponse(null, 'Student moved to trash', 204);
    }

    /**
     * Hard delete (permanent removal).
     * Restricted to teacher/admin only.
     */
    public function forceDestroy(Student $student): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, ['admin', 'teacher'])) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $student->forceDelete();
        return $this->successResponse(null, 'Student permanently deleted', 204);
    }
}
