<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * Show all trashed records in one unified view.
     */
    public function index()
    {
        $guardians = Guardian::whereNotNull('trashed_at')->with('students')->paginate(10);
        $students = Student::whereNotNull('trashed_at')->with('progressRecords')->paginate(10);
        $progressRecords = ProgressRecord::whereNotNull('trashed_at')->with(['student','week'])->paginate(10);
        $weeklySummaries = WeeklySummary::whereNotNull('trashed_at')->with(['student','week'])->paginate(10);

        return view('trash', compact('guardians','students','progressRecords','weeklySummaries'));
    }

    /**
     * Restore a trashed record dynamically.
     */
    public function restore($type, $id)
    {
        switch ($type) {
            case 'guardian':
                $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);
                $guardian->restoreFromTrash();
                foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
                    $student->restoreFromTrash();
                }
                break;

            case 'student':
                $student = Student::whereNotNull('trashed_at')->findOrFail($id);
                $student->restoreFromTrash();
                break;

            case 'progress':
                $record = ProgressRecord::whereNotNull('trashed_at')->findOrFail($id);
                $record->restoreFromTrash();
                break;

            case 'summary':
                $summary = WeeklySummary::whereNotNull('trashed_at')->findOrFail($id);
                $summary->restoreFromTrash();
                break;
        }

        return back()->with('success', ucfirst($type).' restored successfully.');
    }

    /**
     * Permanently delete a trashed record dynamically.
     */
    public function forceDelete($type, $id)
    {
        switch ($type) {
            case 'guardian':
                $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);
                foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
                    $student->hardDelete();
                }
                $guardian->hardDelete();
                break;

            case 'student':
                $student = Student::whereNotNull('trashed_at')->findOrFail($id);
                $student->hardDelete();
                break;

            case 'progress':
                $record = ProgressRecord::whereNotNull('trashed_at')->findOrFail($id);
                $record->hardDelete();
                break;

            case 'summary':
                $summary = WeeklySummary::whereNotNull('trashed_at')->findOrFail($id);
                $summary->hardDelete();
                break;
        }

        return back()->with('success', ucfirst($type).' permanently deleted.');
    }
}
