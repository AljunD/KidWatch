<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProgressResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Student $student, Request $request): JsonResponse
    {
        $week = $request->query('week');
        $query = $student->progressRecords();

        if ($week) {
            $query->where('week_id', $week);
        }

        $progress = $query->paginate(10);

        return $this->successResponse(ProgressResource::collection($progress), 'Progress records retrieved successfully');
    }
}
