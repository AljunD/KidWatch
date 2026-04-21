<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Week;

class WeekController extends Controller
{
    /**
     * Display all weeks.
     */
    public function index()
    {
        $weeks = Week::with(['progressRecords', 'weeklySummaries'])
            ->orderBy('week_number')
            ->get();

        return view('students.progress', compact('weeks'));
    }

    /**
     * Store a newly created week.
     */
    public function store(Request $request)
    {
        $request->validate([
            'week_number' => 'required|integer|unique:weeks,week_number',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        Week::create($request->only('week_number', 'start_date', 'end_date'));

        return redirect()
            ->route('progress')
            ->with('success', 'New week created successfully!');
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
