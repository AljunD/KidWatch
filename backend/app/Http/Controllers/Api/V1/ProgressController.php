<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProgressResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Progress Controller
 *
 * Handles retrieval of progress records for a guardian's students.
 */
class ProgressController extends Controller
{
    /**
     * List progress records for a student.
     *
     * @param Student $student The student model (resolved via route-model binding).
     * @param Request $request The HTTP request instance.
     *
     * @return JsonResponse
     */
    public function index(Student $student, Request $request): JsonResponse
    {
        $week = $request->query('week');

        $query = $student->progressRecords()->whereNull('trashed_at');

        if ($week) {
            $query->where('week_id', $week);
        }

        $progress = $query->paginate(10);

        return $this->successResponse(
            ProgressResource::collection($progress),
            'Progress records retrieved successfully',
            200,
            [
                'pagination' => [
                    'current_page' => $progress->currentPage(),
                    'last_page'    => $progress->lastPage(),
                    'per_page'     => $progress->perPage(),
                    'total'        => $progress->total(),
                ]
            ]
        );
    }
}
