<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreProgressRequest;
use App\Http\Resources\ProgressResource;
use App\Models\Student;
use App\Models\ProgressRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgressController extends Controller
{
    public function index(Student $student, Request $request): JsonResponse
    {
        $week = $request->query('week');
        $query = $student->progressRecords();

        if ($week) {
            $query->where('week_number', $week);
        }

        $progress = $query->paginate(10);
        return $this->successResponse(ProgressResource::collection($progress));
    }

    public function store(StoreProgressRequest $request, Student $student): JsonResponse
    {
        $data = $request->validated();
        $data['student_id'] = $student->id;

        $record = ProgressRecord::create($data);

        Cache::forget("student_summary_{$student->id}_week_{$record->week_number}");

        return $this->successResponse(new ProgressResource($record), 'Progress recorded', 201);
    }
}
