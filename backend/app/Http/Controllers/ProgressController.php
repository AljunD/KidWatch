<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use App\Models\Week;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgressController extends Controller
{
    public function index()
    {
        $weeks = Week::with(['progressRecords', 'weeklySummaries'])
            ->orderBy('week_number')
            ->get();

        $students = Student::with('progressRecords')->get();

        // Subjects are now fixed (not plucked from recommendation_engine_configs)
        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings = ProgressRecord::RATINGS;

        return view('progress', compact('weeks', 'students', 'subjects', 'ratings'));
    }

    public function viewAll(Request $request)
    {
        $studentId = $request->student_id;

        $weeks = Week::with('weeklySummaries')
            ->orderBy('week_number')
            ->get();

        $today = Carbon::today();
        $currentWeek = $weeks->first(fn($week) => $today->between($week->start_date, $week->end_date));

        if ($currentWeek) {
            $weeks = collect([$currentWeek])
                ->merge($weeks->where('id', '!=', $currentWeek->id));
        }

        $student = Student::with('progressRecords')->findOrFail($studentId);

        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings = ProgressRecord::RATINGS;

        return view('progress.view-all', compact('weeks', 'student', 'subjects', 'ratings'));
    }

    public function create(Request $request)
    {
        $student = Student::findOrFail($request->student_id);
        $week = Week::findOrFail($request->week_id);

        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings = ProgressRecord::RATINGS;

        return view('progress.create', compact('student', 'week', 'subjects', 'ratings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'week_id'      => 'required|exists:weeks,id',
            'subject'      => 'required|string|max:50',
            'rating_level' => 'required|integer|min:0|max:4',
            'remarks'      => 'nullable|string|max:500',
        ]);

        $exists = ProgressRecord::where('student_id', $validated['student_id'])
            ->where('week_id', $validated['week_id'])
            ->where('subject', $validated['subject'])
            ->exists();

        if ($exists) {
            return redirect()->route('progress')
                ->with('error', 'This subject has already been graded for this week.');
        }

        ProgressRecord::create($validated);

        return redirect()->route('progress')
            ->with('success', 'Progress record added successfully.');
    }

    public function edit(ProgressRecord $progressRecord)
    {
        $students = Student::all();
        $weeks = Week::all();

        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings = ProgressRecord::RATINGS;

        return view('progress.edit', compact('progressRecord', 'students', 'weeks', 'subjects', 'ratings'));
    }

    public function update(Request $request, ProgressRecord $progressRecord)
    {
        $validated = $request->validate([
            'rating_level' => 'required|integer|min:0|max:4',
            'remarks'      => 'nullable|string|max:500',
        ]);

        $progressRecord->update($validated);

        return redirect()->route('progress')->with('success', 'Progress record updated successfully.');
    }

    public function destroy(ProgressRecord $progressRecord)
    {
        $progressRecord->delete();

        return redirect()->route('progress')->with('success', 'Progress record deleted successfully.');
    }

    public function view($studentId, $weekId)
    {
        $student = Student::with(['progressRecords' => function ($query) use ($weekId) {
            $query->where('week_id', $weekId);
        }])->findOrFail($studentId);

        $week = Week::findOrFail($weekId);

        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings = ProgressRecord::RATINGS;

        return view('progress.view', compact('student', 'week', 'subjects', 'ratings'));
    }

    public function showRecommendation($studentId = null, $weekId = null)
    {
        if (!$studentId || !$weekId) {
            return view('recommendation');
        }

        $student = Student::findOrFail($studentId);
        $week = Week::findOrFail($weekId);

        $summary = WeeklySummary::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->first();

        return view('recommendation', compact('student', 'week', 'summary'));
    }
}
