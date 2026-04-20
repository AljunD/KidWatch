<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;   // ✅ Import Mail facade
use App\Mail\EnrollmentMail;           // ✅ Import your Mailable

class StudentController extends Controller
{
    public function index()
    {
        // ✅ Ensure guardian.contact_number is eager loaded
        $students = Student::with(['guardian.user'])->paginate(20);
        return view('student', compact('students'));
    }

    public function storeWithGuardian(Request $request)
    {
        $validated = $request->validate([
            'student_first_name'     => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'student_middle_name'    => 'nullable|string|max:255|regex:/^[\pL\s\-]+$/u',
            'student_last_name'      => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'student_gender'         => 'required|in:male,female',
            'student_date_of_birth'  => 'required|date|before:today',
            'student_nationality'    => 'required|string|max:100',
            'student_religion'       => 'required|string|max:100',

            'guardian_first_name'    => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'guardian_middle_name'   => 'nullable|string|max:255|regex:/^[\pL\s\-]+$/u',
            'guardian_last_name'     => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'guardian_email'         => 'required|email|regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/|max:255',
            'guardian_relationship'  => 'required|string|max:50',
            // ✅ Fixed validation syntax for contact number
            'guardian_contact_number'=> 'required|string|regex:/^09\d{9}$/|digits:11',
            'guardian_address'       => 'required|string|max:500',
        ]);

        try {
            [$student, $randomPassword, $guardian, $user, $isNewUser] = DB::transaction(function () use ($validated) {
                $randomPassword = null;
                $isNewUser = false;

                $user = User::where('email', $validated['guardian_email'])->first();

                if (!$user) {
                    $isNewUser = true;
                    $randomPassword = bin2hex(random_bytes(8));

                    $user = User::create([
                        'email'    => $validated['guardian_email'],
                        'password' => Hash::make($randomPassword),
                        'role'     => 'guardian',
                        'is_active'=> true,
                    ]);

                    $guardian = Guardian::create([
                        'user_id'               => $user->id,
                        'first_name'            => $validated['guardian_first_name'],
                        'middle_name'           => $validated['guardian_middle_name'],
                        'last_name'             => $validated['guardian_last_name'],
                        'relationship_to_child' => $validated['guardian_relationship'],
                        'contact_number'        => $validated['guardian_contact_number'],
                        'address'               => $validated['guardian_address'],
                    ]);
                } else {
                    $guardian = $user->guardian;
                }

                $student = Student::create([
                    'guardian_id'   => $guardian->id,
                    'first_name'    => $validated['student_first_name'],
                    'middle_name'   => $validated['student_middle_name'],
                    'last_name'     => $validated['student_last_name'],
                    'gender'        => $validated['student_gender'],
                    'date_of_birth' => $validated['student_date_of_birth'],
                    'nationality'   => $validated['student_nationality'],
                    'religion'      => $validated['student_religion'],
                ]);

                return [$student, $randomPassword, $guardian, $user, $isNewUser];
            });

            if ($isNewUser && $randomPassword) {
                Mail::to($user->email)->send(new EnrollmentMail($guardian, $student, $randomPassword));
            }

            return $request->ajax() || $request->wantsJson()
                ? response()->json([
                    'success'            => true,
                    'student'            => $student->only(['id', 'first_name', 'last_name']),
                    'temporary_password' => $randomPassword ?? 'Existing Account'
                ])
                : redirect()->route('students')->with('success', 'Enrollment successful!');
        } catch (\Throwable $e) {
            Log::error('Enrollment failed', ['error' => $e->getMessage()]);
            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Enrollment failed. Please try again.'], 422)
                : redirect()->route('students')->with('error', 'Enrollment failed. Please try again.');
        }
    }

    /**
     * Update student + primary guardian (email and contact number now optional).
     */
    public function update(Request $request, $id)
    {
        $currentUserId = Student::findOrFail($id)->guardian?->user?->id ?? null;

        $rules = [
            'student_first_name'    => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'student_middle_name'   => 'nullable|string|max:255|regex:/^[\pL\s\-]+$/u',
            'student_last_name'     => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'student_gender'        => 'required|in:male,female',
            'student_date_of_birth' => 'required|date|before:today',
            'student_nationality'   => 'required|string|max:100',
            'student_religion'      => 'required|string|max:100',
            'guardian_first_name'   => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'guardian_middle_name'  => 'nullable|string|max:255|regex:/^[\pL\s\-]+$/u',
            'guardian_last_name'    => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'guardian_email'        => 'sometimes|nullable|email|max:255',
            'guardian_relationship' => 'required|string|max:50',
            // ✅ Contact number optional, validated only if filled
            'guardian_contact_number' => [
                'sometimes',
                'nullable',
                'regex:/^09\d{9}$/',
                'digits:11',
            ],
            'guardian_address'      => 'required|string|max:500',
            'guardian_password'              => 'nullable|min:8|confirmed',
            'guardian_password_confirmation' => 'nullable|required_with:guardian_password',
        ];

        if ($request->filled('guardian_email')) {
            $rules['guardian_email'] = [
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($currentUserId),
            ];
        }

        $validated = $request->validate($rules);

        try {
            $student = DB::transaction(function () use ($validated, $request, $id) {
                $student = Student::findOrFail($id);

                $student->update([
                    'first_name'    => $validated['student_first_name'],
                    'middle_name'   => $validated['student_middle_name'],
                    'last_name'     => $validated['student_last_name'],
                    'gender'        => $validated['student_gender'],
                    'date_of_birth' => $validated['student_date_of_birth'],
                    'nationality'   => $validated['student_nationality'],
                    'religion'      => $validated['student_religion'],
                ]);

                $primaryGuardian = $student->guardian;
                if ($primaryGuardian) {
                    // ✅ Build update array without forcing contact_number to null
                    $updateData = [
                        'first_name'            => $validated['guardian_first_name'],
                        'middle_name'           => $validated['guardian_middle_name'],
                        'last_name'             => $validated['guardian_last_name'],
                        'relationship_to_child' => $validated['guardian_relationship'],
                        'address'               => $validated['guardian_address'],
                    ];

                    if ($request->filled('guardian_contact_number')) {
                        $updateData['contact_number'] = $validated['guardian_contact_number'];
                    }

                    $primaryGuardian->update($updateData);

                    if (!empty($validated['guardian_email'] ?? '')) {
                        $user = $primaryGuardian->user;
                        if ($user) {
                            $user->update(['email' => $validated['guardian_email']]);
                            Log::info('Guardian email updated', [
                                'guardian_id' => $primaryGuardian->id,
                                'new_email'   => $validated['guardian_email']
                            ]);
                        }
                    }

                                        if (!empty($validated['guardian_password'])) {
                        $user = $primaryGuardian->user;
                        if ($user) {
                            $user->update(['password' => Hash::make($validated['guardian_password'])]);
                            Log::info('Guardian password reset', ['guardian_id' => $primaryGuardian->id]);
                        }
                    }
                }

                return $student;
            });

            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => true])
                : redirect()->route('students')->with('success', 'Student updated successfully!');
        } catch (\Throwable $e) {
            Log::error('Update failed', ['error' => $e->getMessage()]);
            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Update failed. Please try again.'], 422)
                : redirect()->route('students')->with('error', 'Update failed. Please try again.');
        }
    }

    /**
     * Soft delete with audit logging.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();

            Log::info('Student soft-deleted', ['student_id' => $id]);

            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => true, 'message' => 'Student moved to trash successfully.'])
                : redirect()->route('students')->with('success', 'Student moved to trash successfully.');
        } catch (\Throwable $e) {
            Log::error('Delete failed', ['error' => $e->getMessage()]);
            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Delete failed. Please try again.'], 422)
                : redirect()->route('students')->with('error', 'Delete failed. Please try again.');
        }
    }

    /**
     * Trash, Restore, Force Delete (unchanged but paginated).
     */
    public function trash()
    {
        $students = Student::onlyTrashed()->paginate(20);
        return view('trash', compact('students'));
    }

    public function restore($id)
    {
        Student::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('students.trash')->with('success', 'Student restored successfully.');
    }

    public function forceDelete($id)
    {
        Student::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('students.trash')->with('success', 'Student permanently deleted.');
    }
}

