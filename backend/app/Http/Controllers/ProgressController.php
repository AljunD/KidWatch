<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use App\Models\Week;
use App\Models\RecommendationEngineConfig;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Show progress records for all weeks.
     */
    public function index()
    {
        $weeks = Week::with(['progressRecords', 'weeklySummaries'])
            ->orderBy('week_number')
            ->get();

        $students = Student::with('progressRecords')->get();
        $subjects = RecommendationEngineConfig::distinct()->pluck('subject');
        $ratings = ProgressRecord::RATINGS;

        return view('progress', compact('weeks', 'students', 'subjects', 'ratings'));
    }
    /**
     * Show all progress records for all students across all weeks.
     */
    public function viewAll(Request $request)
    {
        // Require a student_id to be passed
        $studentId = $request->student_id;

        $weeks = Week::with('weeklySummaries')
            ->orderBy('week_number')
            ->get();

        $student = Student::with('progressRecords')->findOrFail($studentId);
        $subjects = RecommendationEngineConfig::distinct()->pluck('subject');
        $ratings = ProgressRecord::RATINGS;

        return view('progress.view-all', compact('weeks', 'student', 'subjects', 'ratings'));
    }

    /**
     * Show the form for creating a new progress record for a specific student/week.
     */
    public function create(Request $request)
    {
        $student = Student::findOrFail($request->student_id);
        $week = Week::findOrFail($request->week_id);
        $subjects = RecommendationEngineConfig::distinct()->pluck('subject');
        $ratings = ProgressRecord::RATINGS;

        return view('progress.create', compact('student', 'week', 'subjects', 'ratings'));
    }

    /**
     * Store new progress records (one per subject).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'week_id'      => 'required|exists:weeks,id',
            'subject'      => 'required|string|max:50',
            'rating_level' => 'required|integer|min:0|max:4', // allow 0 for "No Classes"
            'remarks'      => 'nullable|string|max:500',
        ]);

        // Prevent duplicate grading for same student/week/subject
        $exists = ProgressRecord::where('student_id', $validated['student_id'])
            ->where('week_id', $validated['week_id'])
            ->where('subject', $validated['subject'])
            ->exists();

        if ($exists) {
            return redirect()->route('progress')
                ->with('error', 'This subject has already been graded for this week.');
        }

        ProgressRecord::create([
            'student_id'   => $validated['student_id'],
            'week_id'      => $validated['week_id'],
            'subject'      => $validated['subject'],
            'rating_level' => $validated['rating_level'],
            'remarks'      => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('progress')
            ->with('success', 'Progress record added successfully.');
    }

    /**
     * Show the form for editing an existing progress record.
     */
    public function edit(ProgressRecord $progressRecord)
    {
        $students = Student::all();
        $weeks = Week::all();
        $subjects = RecommendationEngineConfig::distinct()->pluck('subject');
        $ratings = ProgressRecord::RATINGS;

        return view('progress.edit', compact('progressRecord', 'students', 'weeks', 'subjects', 'ratings'));
    }

    /**
     * Update an existing progress record.
     */
    public function update(Request $request, ProgressRecord $progressRecord)
    {
        $validated = $request->validate([
            'rating_level' => 'required|integer|min:0|max:4', // ✅ allow 0
            'remarks'      => 'nullable|string|max:500',
        ]);

        $progressRecord->update($validated);

        return redirect()->route('progress')->with('success', 'Progress record updated successfully.');
    }

    /**
     * Delete a progress record.
     */
    public function destroy(ProgressRecord $progressRecord)
    {
        $progressRecord->delete();

        return redirect()->route('progress')->with('success', 'Progress record deleted successfully.');
    }

    /**
     * Show all progress records for a student in a given week.
     */
    public function view($studentId, $weekId)
    {
        $student = Student::with(['progressRecords' => function ($query) use ($weekId) {
            $query->where('week_id', $weekId);
        }])->findOrFail($studentId);

        $week = Week::findOrFail($weekId);
        $subjects = RecommendationEngineConfig::distinct()->pluck('subject');
        $ratings = ProgressRecord::RATINGS;

        return view('progress.view', compact('student', 'week', 'subjects', 'ratings'));
    }

    /**
     * Show weekly summary for a student.
     */
    public function summary($studentId, $weekId)
    {
        $summary = WeeklySummary::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->first();

        return response()->json([
            'summary' => $summary ? $summary->summary_text : 'No summary available.',
        ]);
    }
}
