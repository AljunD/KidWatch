<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\WeeklySummaryResource;
use App\Models\Student;
use App\Models\WeeklySummary;
use App\Services\SummaryGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SummaryController extends Controller
{
    public function __construct(private readonly SummaryGeneratorService $summaryGenerator) {}

    public function show(Student $student, int $week): JsonResponse
    {
        $cacheKey = "student_summary_{$student->id}_week_{$week}";

        $summary = Cache::remember($cacheKey, 3600, function () use ($student, $week) {
            return WeeklySummary::firstOrCreate(
                ['student_id' => $student->id, 'week_number' => $week],
                ['summary_text' => $this->summaryGenerator->generate($student, $week)]
            );
        });

        return $this->successResponse(new WeeklySummaryResource($summary));
    }


    public function regenerate(Student $student, int $week): JsonResponse
    {
        $summaryText = $this->summaryGenerator->generate($student, $week);

        $summary = WeeklySummary::updateOrCreate(
            ['student_id' => $student->id, 'week_number' => $week],
            ['summary_text' => $summaryText]
        );

        Cache::forget("student_summary_{$student->id}_week_{$week}");

        return $this->successResponse(new WeeklySummaryResource($summary), 'Summary regenerated');
    }
}
