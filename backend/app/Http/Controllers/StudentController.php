<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::whereNull('trashed_at')
            ->whereHas('guardian', fn($q) => $q->whereNull('trashed_at'))
            ->with('guardian')
            ->paginate(20);

        return view('student', compact('students'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'student_first_name'    => 'required|string|max:255',
            'student_middle_name'   => 'nullable|string|max:255',
            'student_last_name'     => 'required|string|max:255',
            'student_gender'        => 'required|in:male,female',
            'student_date_of_birth' => 'required|date|before:today',
            'student_nationality'   => 'required|string|max:100',
            'student_religion'      => 'required|string|max:100',
            'student_photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        try {
            $student = Student::findOrFail($id);

            if ($request->hasFile('student_photo')) {
                if ($student->photo_path && Storage::disk('public')->exists($student->photo_path)) {
                    Storage::disk('public')->delete($student->photo_path);
                }
                $student->photo_path = $request->file('student_photo')->store('students/photos', 'public');
            }

            $student->update([
                'first_name'    => $validated['student_first_name'],
                'middle_name'   => $validated['student_middle_name'],
                'last_name'     => $validated['student_last_name'],
                'gender'        => $validated['student_gender'],
                'date_of_birth' => $validated['student_date_of_birth'],
                'nationality'   => $validated['student_nationality'],
                'religion'      => $validated['student_religion'],
                'photo_path'    => $student->photo_path,
            ]);

            return $request->ajax()
                ? response()->json(['success' => true])
                : redirect()->route('students')->with('success', 'Student updated successfully!');
        } catch (\Throwable $e) {
            Log::error('Update failed', ['error' => $e->getMessage()]);

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Update failed. Please try again.'], 422)
                : redirect()->route('students')->with('error', 'Update failed. Please try again.');
        }
    }

    public function trash($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->trashed_at = now();
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Student moved to trash.',
                'student_id' => $student->id
            ]);
        } catch (\Throwable $e) {
            Log::error('Trash failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to trash student.'
            ], 422);
        }
    }

    public function trashList()
    {
        $students = Student::whereNotNull('trashed_at')->paginate(20);
        return view('trash', compact('students'));
    }

    public function restore($id)
    {
        $student = Student::whereNotNull('trashed_at')->findOrFail($id);
        $student->restoreFromTrash();

        return redirect()->route('students.trash')->with('success', 'Student restored successfully.');
    }

    public function forceDelete($id)
    {
        $student = Student::whereNotNull('trashed_at')->findOrFail($id);
        $student->hardDelete();

        return redirect()->route('students.trash')->with('success', 'Student permanently deleted.');
    }
}
