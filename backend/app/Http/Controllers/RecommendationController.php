<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Week;
use App\Models\ProgressRecord;
use App\Models\RecommendationEngineConfig;
use App\Models\WeeklySummary;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    // ======================
    // 1. LIST PAGE
    // ======================
    public function index()
    {
        // Load all students with guardians
        $students = Student::with('guardian')->get();

        // Load all weeks
        $weeks = Week::all();

        return view('recommendation.index', compact('students', 'weeks'));
    }

    // ======================
    // 2. SHOW DETAILS
    // ======================
    public function show($studentId, $weekId)
    {
        $student = Student::with('guardian')->findOrFail($studentId);
        $week = Week::findOrFail($weekId);

        $summary = WeeklySummary::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->first();

        // Get progress records for this student/week
        $records = ProgressRecord::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->get();

        // Identify weak subjects (rating <= 1)
        $weakSubjects = $records
            ->where('rating_level', '<=', 1)
            ->pluck('subject')
            ->toArray();

        return view('recommendation.show', compact(
            'student',
            'week',
            'summary',
            'weakSubjects'
        ));
    }

    // ======================
    // 3. GENERATE SUMMARY (THE MAGIC)
    // ======================
    public function generateSummary($studentId, $weekId)
    {
        $records = ProgressRecord::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->get()
            ->keyBy('subject');

        // Ensure all 4 subjects are rated
        if ($records->count() < 4) {
            return back()->with('error', 'Complete all subject ratings first.');
        }

        // Safely fetch ratings
        $math     = optional($records->get('Math'))->rating_level ?? 0;
        $science  = optional($records->get('Science'))->rating_level ?? 0;
        $english  = optional($records->get('English'))->rating_level ?? 0;
        $filipino = optional($records->get('Filipino'))->rating_level ?? 0;

        // 🔥 Get pre-generated expert system result
        $config = RecommendationEngineConfig::where([
            'math_rating'     => $math,
            'science_rating'  => $science,
            'english_rating'  => $english,
            'filipino_rating' => $filipino,
        ])->first();

        if (!$config) {
            return back()->with('error', 'No recommendation found for this rating combination.');
        }

        // Save or update weekly summary
        WeeklySummary::updateOrCreate(
            [
                'student_id' => $studentId,
                'week_id'    => $weekId,
            ],
            [
                'summary_text' => $config->intervention_text,
            ]
        );

        return redirect()->route('recommendation.detail', [$studentId, $weekId])
            ->with('success', 'Recommendation generated successfully!');
    }
}
