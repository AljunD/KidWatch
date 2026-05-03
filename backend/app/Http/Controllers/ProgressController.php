<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use App\Models\Week;
use Illuminate\Http\Request;
use App\Services\SummaryGeneratorService;

class ProgressController extends Controller
{
    public function index()
    {
        $students = Student::whereNull('trashed_at')
            ->whereHas('guardian', fn($q) => $q->whereNull('trashed_at'))
            ->with(['progressRecords' => fn($q) => $q->whereNull('trashed_at')])
            ->get();

        $weeks = $students->isEmpty()
            ? collect()
            : Week::with([
                'progressRecords' => fn($q) => $q->whereNull('trashed_at'),
                'weeklySummaries' => fn($q) => $q->whereNull('trashed_at')
            ])->orderBy('week_number')->get();

        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings  = ProgressRecord::RATINGS;

        return view('progress', compact('weeks', 'students', 'subjects', 'ratings'));
    }

    public function create(Request $request)
    {
        $student  = Student::findOrFail($request->student_id);
        $week     = Week::findOrFail($request->week_id);
        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings  = ProgressRecord::RATINGS;

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
            ->whereNull('trashed_at')
            ->exists();

        if ($exists) {
            return redirect()->route('progress')
                ->with('error', 'This subject has already been graded for this week.');
        }

        ProgressRecord::create($validated);

        return redirect()->route('progress')->with('success', 'Progress record added successfully.');
    }

    public function edit($studentId, $weekId)
    {
        $student  = Student::findOrFail($studentId);
        $week     = Week::findOrFail($weekId);
        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings  = ProgressRecord::RATINGS;

        $records = ProgressRecord::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->get()
            ->keyBy('subject');

        return view('progress.edit', compact(
            'student', 'week', 'subjects', 'ratings', 'records'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rating_level' => 'required|integer|min:0|max:4',
            'remarks'      => 'nullable|string|max:500',
        ]);

        $progressRecord = ProgressRecord::findOrFail($id);
        $progressRecord->update($validated);

        return redirect()->back()->with('success', $progressRecord->subject.' updated successfully.');
    }

    public function destroy(ProgressRecord $progressRecord)
    {
        $progressRecord->trash();
        return redirect()->route('progress')->with('success', 'Progress record moved to trash successfully.');
    }

    public function restore($id)
    {
        $record = ProgressRecord::whereNotNull('trashed_at')->findOrFail($id);
        $record->restoreFromTrash();

        return redirect()->route('progress')->with('success', 'Progress record restored successfully.');
    }

    public function forceDelete($id)
    {
        $record = ProgressRecord::whereNotNull('trashed_at')->findOrFail($id);
        $record->hardDelete();

        return redirect()->route('progress')->with('success', 'Progress record permanently deleted.');
    }

    public function view($studentId, $weekId)
    {
        $student  = Student::with(['progressRecords' => fn($q) => $q->where('week_id', $weekId)->whereNull('trashed_at')])
            ->findOrFail($studentId);

        $week     = Week::findOrFail($weekId);
        $subjects = ['Math', 'Science', 'English', 'Filipino'];
        $ratings  = ProgressRecord::RATINGS;

        return view('progress.view', compact('student', 'week', 'subjects', 'ratings'));
    }

    public function viewAll(Request $request)
    {
        $studentId = $request->query('student_id');

        $student = Student::with(['progressRecords' => fn($q) => $q->whereNull('trashed_at')])
            ->findOrFail($studentId);

        $weeks = Week::with([
            'progressRecords' => fn($q) => $q->where('student_id', $studentId)->whereNull('trashed_at'),
            'weeklySummaries' => fn($q) => $q->where('student_id', $studentId)->whereNull('trashed_at')
        ])->orderBy('week_number')->get();

        $subjects  = ['Math', 'Science', 'English', 'Filipino'];
        $ratings   = ProgressRecord::RATINGS;
        $summaries = WeeklySummary::where('student_id', $studentId)
            ->whereNull('trashed_at')
            ->get()
            ->groupBy(fn($s) => $s->student_id.'-'.$s->week_id);

        return view('progress.view-all', compact('student', 'weeks', 'subjects', 'ratings', 'summaries'));
    }

    public function generateRecommendation($studentId, $weekId)
    {
        $student = Student::findOrFail($studentId);

        $records = ProgressRecord::where('student_id', $studentId)
            ->where('week_id', $weekId)
            ->whereNull('trashed_at')
            ->get();

        if ($records->count() < 4) {
            return back()->with('error', 'Complete all subject ratings first.');
        }

        $result = app(SummaryGeneratorService::class)->generate($student, $weekId);

        WeeklySummary::updateOrCreate(
            ['student_id' => $studentId, 'week_id' => $weekId],
            [
                'summary_text'    => $result['summary'],
                'activities_text' => json_encode($result['activities'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]
        );

        return back()->with('success', 'Weekly summary generated successfully!');
    }
}
