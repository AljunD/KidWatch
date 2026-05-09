<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProgressResource;
use App\Models\Student;
use App\Models\Week;
use App\Models\ProgressRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Validate guardian and student ownership.
     */
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
     * List progress records for a student in fixed subject order.
     * Always include the latest week, even if no progress exists.
     */
    public function index(Student $student, Request $request): JsonResponse
    {
        if ($resp = $this->validateGuardianAccess($student)) {
            return $resp;
        }

        $weekId = $request->query('week');
        $query = $student->progressRecords()
            ->whereNull('trashed_at')
            ->with('week');

        if ($weekId) {
            $query->where('week_id', $weekId);
        }

        $progress = $query->get();

        $subjectOrder = ['Math', 'Science', 'English', 'Filipino'];
        $progress = $progress->sortBy(function ($record) use ($subjectOrder) {
            return array_search($record->subject, $subjectOrder);
        })->values();

        $latestWeek = Week::orderBy('week_number', 'desc')->first();
        if (!$latestWeek) {
            return $this->successResponse([], 'No weeks defined yet');
        }

        $latestWeekProgress = $progress->where('week_id', $latestWeek->id);

        if ($latestWeekProgress->isEmpty()) {
            $placeholder = new ProgressRecord([
                'student_id'   => $student->id,
                'week_id'      => $latestWeek->id,
                'subject'      => null,
                'rating_level' => null,
                'remarks'      => null,
            ]);

            $placeholder->setRelation('week', $latestWeek);

            $progress->prepend($placeholder);
        }

        $perPage = 10;
        $page = (int) $request->query('page', 1);
        $paged = $progress->forPage($page, $perPage);

        $meta = [
            'pagination' => [
                'current_page' => $page,
                'last_page'    => ceil($progress->count() / $perPage),
                'per_page'     => $perPage,
                'total'        => $progress->count(),
            ]
        ];

        return $this->successResponse(
            ProgressResource::collection($paged),
            'Progress records retrieved successfully',
            200,
            $meta
        );
    }
}
