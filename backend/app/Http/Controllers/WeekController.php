<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Week;
use App\Models\Student;
use App\Models\ProgressRecord;

class WeekController extends Controller
{
    /**
     * Display all weeks.
     */
    public function index()
    {
        // ✅ Only active students with active guardians
        $students = Student::whereNull('trashed_at')
            ->whereHas('guardian', fn($q) => $q->whereNull('trashed_at'))
            ->get();

        // ✅ If no students, delete all weeks and return empty
        if ($students->isEmpty()) {
            Week::query()->delete();
            $weeks = collect(); // empty collection
        } else {
            $weeks = Week::with(['progressRecords', 'weeklySummaries'])
                ->orderBy('week_number')
                ->get();
        }

        return view('students.progress', compact('weeks'));
    }

    /**
     * Store a newly created week.
     * Only allowed if all students have ratings for every subject in the latest week.
     */
    public function store(Request $request)
    {
        // ✅ Only active students with active guardians
        $students = Student::whereNull('trashed_at')
            ->whereHas('guardian', fn($q) => $q->whereNull('trashed_at'))
            ->get();

        // ✅ Block creation if no students exist
        if ($students->isEmpty()) {
            return redirect()
                ->route('progress')
                ->with('error', 'Cannot create a new week: No active students exist.');
        }

        // Get the latest week
        $latestWeek = Week::orderBy('week_number', 'desc')->first();

        if ($latestWeek) {
            $subjects = ['Math', 'Science', 'English', 'Filipino'];

            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    $hasRecord = ProgressRecord::where('student_id', $student->id)
                        ->where('week_id', $latestWeek->id)
                        ->where('subject', $subject)
                        ->whereNull('trashed_at')
                        ->exists();

                    if (!$hasRecord) {
                        return redirect()
                            ->route('progress')
                            ->with('error', "Cannot create a new week: Student {$student->first_name} {$student->last_name} is missing a rating for {$subject} in Week {$latestWeek->week_number}.");
                    }
                }
            }
        }

        // ✅ Auto-increment week_number
        $nextWeekNumber = $latestWeek ? $latestWeek->week_number + 1 : 1;

        // Validate new week input
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // ✅ Prevent overlapping weeks
        $overlap = Week::where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date);
                  });
        })->exists();

        if ($overlap) {
            return redirect()
                ->route('progress')
                ->with('error', 'The new week overlaps with an existing week. Please adjust the dates.');
        }

        Week::create([
            'week_number' => $nextWeekNumber,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        return redirect()
            ->route('progress')
            ->with('success', "Week {$nextWeekNumber} created successfully!");
    }

    /**
     * Delete a week.
     */
    public function destroy(Week $week)
    {
        $week->delete();

        return redirect()
            ->route('progress')
            ->with('success', 'Week deleted successfully!');
    }
}
