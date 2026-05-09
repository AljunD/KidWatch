<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Student;
use App\Models\Week;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProgressHistoryController extends Controller
{
    private function validateGuardianAccess(Student $student): ?JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student');
        }

        return null;
    }

    /**
     * List all weeks for a student with progress status.
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function index(Student $student): JsonResponse
    {
        if ($resp = $this->validateGuardianAccess($student)) {
            return $resp;
        }
        $weeks = Week::orderBy('week_number', 'desc')->get();
        $latestWeek = $weeks->first();

        $data = $weeks->map(function ($week) use ($student, $latestWeek) {
            $progressCount = $student->progressRecords()
                ->where('week_id', $week->id)
                ->whereNull('trashed_at')
                ->count();

            if ($week->id === $latestWeek->id) {
                $status = 'Current Week';
            } else {
                $status = 'Completed';
            }

            return [
                'week_id'        => $week->id,
                'week_number'    => $week->week_number,
                'start_date'     => $week->start_date ? $week->start_date->toDateString() : null,
                'end_date'       => $week->end_date ? $week->end_date->toDateString() : null,
                'status'         => $status,
                'progress_count' => $progressCount,
            ];
        });

        return $this->successResponse(
            $data,
            'Progress history retrieved successfully'
        );
    }
}
