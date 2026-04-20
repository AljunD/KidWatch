<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Show progress records for a given week.
     */
    public function index(Request $request)
    {
        $week = $request->input('week_number', 1);

        $students = Student::with([
            'progressRecords' => function ($query) use ($week) {
                $query->where('week_number', $week);
            },
            'weeklySummaries' => function ($query) use ($week) {
                $query->where('week_number', $week);
            }
        ])->get();

        // Example: generate a simple weeks array
        $weeks = collect(range(1, 6))->map(fn($n) => (object)['number' => $n]);


        return view('progress', [
            'students'      => $students,
            'currentWeek'   => $week,
            'weeklySummary' => WeeklySummary::where('week_number', $week)->first(),
            'subjects'      => ['Mathematics', 'Science', 'English', 'Filipino'],
            'ratings'       => [
                1 => 'Poor',
                2 => 'Good',
                3 => 'Very Good',
                4 => 'Excellent',
            ],
            'weeks'         => $weeks, // <-- pass weeks to the view
        ]);
    }


    /**
     * Store a new progress record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'week_number'  => 'required|integer|min:1',
            'subject'      => 'required|string|max:50',
            'rating_level' => 'required|integer|min:1|max:4',
        ]);

        ProgressRecord::create($validated);

        return redirect()->back()->with('success', 'Progress record added successfully.');
    }

    /**
     * Update an existing progress record.
     */
    public function update(Request $request, ProgressRecord $progressRecord)
    {
        $validated = $request->validate([
            'rating_level' => 'required|integer|min:1|max:4',
        ]);

        $progressRecord->update($validated);

        return redirect()->back()->with('success', 'Progress record updated successfully.');
    }

    /**
     * Delete a progress record.
     */
    public function destroy(ProgressRecord $progressRecord)
    {
        $progressRecord->delete();

        return redirect()->back()->with('success', 'Progress record deleted successfully.');
    }

    /**
     * Show weekly summary for a student.
     */
    public function summary($studentId, $weekNumber)
    {
        $summary = WeeklySummary::where('student_id', $studentId)
            ->where('week_number', $weekNumber)
            ->first();

        return response()->json([
            'summary' => $summary ? $summary->summary_text : 'No summary available.',
        ]);
    }
}
