<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProgressResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * List progress records for a student.
     *
     * @param Student $student
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Student $student, Request $request): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        // ✅ Ensure guardian owns this student and account is active
        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access progress records for this student');
        }

        $weekId = $request->query('week');

        // ✅ Fetch active progress records, eager load week
        $query = $student->progressRecords()
            ->whereNull('trashed_at')
            ->with('week');

        if ($weekId) {
            $query->where('week_id', $weekId);
        }

        $progress = $query->paginate(10);

        if ($progress->isEmpty()) {
            return $this->notFoundResponse('Progress records');
        }

        $meta = [
            'pagination' => [
                'current_page' => $progress->currentPage(),
                'last_page'    => $progress->lastPage(),
                'per_page'     => $progress->perPage(),
                'total'        => $progress->total(),
            ]
        ];

        return $this->successResponse(
            ProgressResource::collection($progress),
            'Progress records retrieved successfully',
            200,
            $meta
        );
    }
}
