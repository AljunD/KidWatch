<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // Show all active students
    public function index()
    {
        $students = Student::whereNull('trashed_at') // ✅ only active students
            ->whereHas('guardian', function ($query) {
                $query->whereNull('trashed_at'); // ✅ only if guardian is active
            })
            ->with(['guardian'])
            ->paginate(20);

        return view('student', compact('students'));
    }

    // Update student + guardian info, with photo upload
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
            'student_photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'guardian_first_name'   => 'required|string|max:255',
            'guardian_middle_name'  => 'nullable|string|max:255',
            'guardian_last_name'    => 'required|string|max:255',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_contact_number' => 'nullable|regex:/^09\d{9}$/|digits:11',
            'guardian_address'      => 'required|string|max:500',
        ]);

        try {
            $student = Student::findOrFail($id);

            // ✅ Handle photo upload
            if ($request->hasFile('student_photo')) {
                if ($student->photo_path && Storage::disk('public')->exists($student->photo_path)) {
                    Storage::disk('public')->delete($student->photo_path);
                }
                $path = $request->file('student_photo')->store('students/photos', 'public');
                $student->photo_path = $path;
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

            if ($student->guardian) {
                $student->guardian->update([
                    'first_name'            => $validated['guardian_first_name'],
                    'middle_name'           => $validated['guardian_middle_name'],
                    'last_name'             => $validated['guardian_last_name'],
                    'relationship_to_child' => $validated['guardian_relationship'],
                    'contact_number'        => $validated['guardian_contact_number'] ?? $student->guardian->contact_number,
                    'address'               => $validated['guardian_address'],
                ]);
            }

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

    // Soft delete student
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->trash();

            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('students')->with('success', 'Student moved to trash successfully.');
        } catch (\Throwable $e) {
            Log::error('Delete failed', ['error' => $e->getMessage()]);

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Delete failed. Please try again.'], 422);
            }

            return redirect()->route('students')->with('error', 'Delete failed. Please try again.');
        }
}

    // Trash list (students + guardians)
    public function trash()
    {
        $students = Student::whereNotNull('trashed_at')->paginate(20);
        $guardians = Guardian::whereNotNull('trashed_at')->paginate(20);

        return view('trash', compact('students', 'guardians'));
    }

    // Restore student
    public function restore($id)
    {
        $student = Student::whereNotNull('trashed_at')->findOrFail($id);
        $student->restoreFromTrash(); // ✅ custom helper
        return redirect()->route('students.trash')->with('success', 'Student restored successfully.');
    }

    // Force delete student
    public function forceDelete($id)
    {
        $student = Student::whereNotNull('trashed_at')->findOrFail($id);
        $student->hardDelete(); // ✅ custom helper
        return redirect()->route('students.trash')->with('success', 'Student permanently deleted.');
    }

    // Restore guardian + cascade restore students
    public function restoreGuardian($id)
    {
        $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);
        $guardian->restoreFromTrash(); // ✅ custom helper

        // Cascade restore linked students
        foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
            $student->restoreFromTrash();
        }

        return redirect()->route('students.trash')->with('success', 'Guardian and linked students restored successfully.');
    }

    // Force delete guardian + cascade delete students
    public function forceDeleteGuardian($id)
    {
        $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);

        // Cascade delete linked students
        foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
            $student->hardDelete();
        }

        $guardian->hardDelete(); // ✅ custom helper

        return redirect()->route('students.trash')->with('success', 'Guardian and linked students permanently deleted.');
    }
}
