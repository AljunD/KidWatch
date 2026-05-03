<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        $guardians       = Guardian::whereNotNull('trashed_at')->with('students')->paginate(10);
        $students        = Student::whereNotNull('trashed_at')->with(['progressRecords','weeklySummaries'])->paginate(10);
        $progressRecords = ProgressRecord::whereNotNull('trashed_at')->with(['student','week'])->paginate(10);
        $weeklySummaries = WeeklySummary::whereNotNull('trashed_at')->with(['student','week'])->paginate(10);

        return view('trash', compact('guardians','students','progressRecords','weeklySummaries'));
    }

    public function restore(string $type, int $id)
    {
        try {
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

                    if (!$student->guardian || $student->guardian->trashed_at !== null) {
                        return back()->with(
                            'error',
                            'Cannot restore student: Guardian is missing or inactive.'
                        );
                    }

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

                default:
                    return back()->with('error', 'Invalid restore type.');
            }

            return back()->with('success', ucfirst($type).' restored successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', ucfirst($type).' restore failed: '.$e->getMessage());
        }
    }

    public function forceDelete(string $type, int $id)
    {
        try {
            switch ($type) {
                case 'guardian':
                    $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);

                    foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
                        $student->hardDelete();
                    }

                    if ($guardian->user) {
                        $guardian->user->delete();
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

                default:
                    return back()->with('error', 'Invalid delete type.');
            }

            return back()->with('success', ucfirst($type).' permanently deleted.');
        } catch (\Throwable $e) {
            return back()->with('error', ucfirst($type).' delete failed: '.$e->getMessage());
        }
    }
}
