<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::whereNull('trashed_at')
            ->with(['students' => fn($q) => $q->whereNull('trashed_at')])
            ->paginate(20);

        return view('guardian', compact('guardians'));
    }

    public function trash()
    {
        $guardians       = Guardian::whereNotNull('trashed_at')->paginate(20);
        $students        = Student::whereNotNull('trashed_at')->paginate(20);
        $progressRecords = ProgressRecord::whereNotNull('trashed_at')->paginate(20);
        $weeklySummaries = WeeklySummary::whereNotNull('trashed_at')->paginate(20);

        return view('trash', compact('guardians', 'students', 'progressRecords', 'weeklySummaries'));
    }

    public function restore($id)
    {
        try {
            $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);
            $guardian->restoreFromTrash();

            foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
                $student->restoreFromTrash();
            }

            return redirect()->route('guardians.trash')
                ->with('success', 'Guardian and linked students restored successfully.');
        } catch (\Throwable $e) {
            Log::error('Guardian restore failed', ['error' => $e->getMessage()]);
            return redirect()->route('guardians.trash')
                ->with('error', 'Guardian restore failed. Please try again.');
        }
    }

    public function forceDelete($id)
    {
        try {
            $guardian = Guardian::whereNotNull('trashed_at')->findOrFail($id);
            
            foreach ($guardian->students()->whereNotNull('trashed_at')->get() as $student) {
                $student->hardDelete();
            }

            if ($guardian->user) {
                $guardian->user->delete();
            }

            $guardian->hardDelete();

            return redirect()->route('guardians.trash')
                ->with('success', 'Guardian, linked students, and email permanently deleted.');
        } catch (\Throwable $e) {
            Log::error('Guardian force delete failed', ['error' => $e->getMessage()]);
            return redirect()->route('guardians.trash')
                ->with('error', 'Guardian force delete failed. Please try again.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'           => 'required|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'contact_number'       => 'required|regex:/^09\d{9}$/|digits:11',
            'address'              => 'required|string|max:255',
            'relationship_to_child'=> 'required|string|max:50',
            'password'             => 'nullable|string|min:8|confirmed',
        ]);

        $defaultPassword = $request->filled('password')
            ? $request->password
            : Str::random(10);

        $user = User::create([
            'email'    => $request->email,
            'password' => bcrypt($defaultPassword),
            'role'     => 'guardian',
        ]);

        Guardian::create([
            'user_id'             => $user->id,
            'first_name'          => $request->first_name,
            'middle_name'         => $request->middle_name,
            'last_name'           => $request->last_name,
            'relationship_to_child'=> $request->relationship_to_child,
            'contact_number'      => $request->contact_number,
            'address'             => $request->address,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'          => true,
                'message'          => 'Guardian created successfully.',
                'default_password' => $request->filled('password') ? null : $defaultPassword,
                'created_at'       => $user->created_at->toDateString(),
                'email_verified_at'=> $user->email_verified_at,
            ]);
        }

        return redirect()->route('guardians.index')
            ->with('success', 'Guardian created successfully. Default password has been sent via email.');
    }

    public function update(Request $request, Guardian $guardian)
    {
        $request->validate([
            'first_name'           => 'required|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'required|string|max:255',
            'contact_number'       => 'required|regex:/^09\d{9}$/|digits:11',
            'address'              => 'required|string|max:255',
            'relationship_to_child'=> 'required|string|max:50',
            'email'                => 'nullable|email|unique:users,email,' . $guardian->user_id,
            'password'             => 'nullable|string|min:8|confirmed',
        ]);

        $guardian->update($request->only([
            'first_name','middle_name','last_name',
            'contact_number','address','relationship_to_child'
        ]));

        if ($request->filled('email') && $request->email !== $guardian->user->email) {
            $guardian->user->email = $request->email;
            $guardian->user->email_verified_at = null;
            $guardian->user->save();
            $guardian->user->sendEmailVerificationNotification();
        }

        if ($request->filled('password')) {
            $guardian->user->password = bcrypt($request->password);
            $guardian->user->save();
        }

        return $request->ajax()
            ? response()->json([
                'success'          => true,
                'message'          => 'Guardian updated successfully. Verification email sent if email was changed.',
                'created_at'       => $guardian->user->created_at->toDateString(),
                'email_verified_at'=> $guardian->user->email_verified_at,
            ])
            : redirect()->route('guardians.index')
                ->with('success', 'Guardian updated successfully. Verification email sent if email was changed.');
    }

    public function destroy(Request $request, Guardian $guardian)
    {
        $guardian->trashed_at = now();
        $guardian->save();

        foreach ($guardian->students as $student) {
            $student->trashed_at = now();
            $student->save();
        }

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => 'Guardian and linked students moved to trash.'])
            : redirect()->route('guardians.index')->with('success', 'Guardian and linked students moved to trash.');
    }

    public function createStudent($guardianId)
    {
        $guardian = Guardian::with('user')->findOrFail($guardianId);
        return view('students.create', compact('guardian'));
    }

    public function storeStudent(Request $request, $guardianId)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'nationality'   => 'required|string|max:100',
            'religion'      => 'required|string|max:100',
            'photo'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $guardian = Guardian::findOrFail($guardianId);
        $student = new Student([
            'guardian_id'   => $guardian->id,
            'first_name'    => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'     => $request->last_name,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'nationality'   => $request->nationality,
            'religion'      => $request->religion,
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students/photos', 'public');
            $student->photo_path = $path;
        }

        $student->save();

        if ($request->ajax()) {
            return response()->json([
                'success'        => true,
                'message'        => 'Student linked to guardian successfully.',
                'student_id'     => $student->id,
                'guardian_id'    => $guardian->id,
                'created_at'     => $student->created_at->toDateString(),
            ]);
        }

        return redirect()->route('guardians.index')
            ->with('success', 'Student linked to guardian successfully.');
    }

    public function storeWithStudent(Request $request)
    {
        $request->validate([
            'guardian_first_name'     => 'required|string|max:255',
            'guardian_middle_name'    => 'nullable|string|max:255',
            'guardian_last_name'      => 'required|string|max:255',
            'guardian_email'          => 'required|email|unique:users,email',
            'guardian_contact_number' => 'required|regex:/^09\d{9}$/|digits:11',
            'guardian_address'        => 'required|string|max:255',
            'guardian_relationship'   => 'required|string|max:50',

            'student_first_name'      => 'required|string|max:255',
            'student_middle_name'     => 'nullable|string|max:255',
            'student_last_name'       => 'required|string|max:255',
            'student_gender'          => 'required|in:male,female',
            'student_date_of_birth'   => 'required|date|before:today',
            'student_nationality'     => 'required|string|max:100',
            'student_religion'        => 'required|string|max:100',
            'student_photo'           => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $defaultPassword = $request->filled('guardian_password')
            ? $request->guardian_password
            : Str::random(10);

        $user = User::create([
            'email'    => $request->guardian_email,
            'password' => bcrypt($defaultPassword),
            'role'     => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id'              => $user->id,
            'first_name'           => $request->guardian_first_name,
            'middle_name'          => $request->guardian_middle_name,
            'last_name'            => $request->guardian_last_name,
            'relationship_to_child'=> $request->guardian_relationship,
            'contact_number'       => $request->guardian_contact_number,
            'address'              => $request->guardian_address,
        ]);

        $student = new Student([
            'guardian_id'   => $guardian->id,
            'first_name'    => $request->student_first_name,
            'middle_name'   => $request->student_middle_name,
            'last_name'     => $request->student_last_name,
            'gender'        => $request->student_gender,
            'date_of_birth' => $request->student_date_of_birth,
            'nationality'   => $request->student_nationality,
            'religion'      => $request->student_religion,
        ]);

        $path = $request->file('student_photo')->store('students/photos', 'public');
        $student->photo_path = $path;
        $student->save();

        if ($request->ajax()) {
            return response()->json([
                'success'             => true,
                'message'             => 'Guardian and student created successfully.',
                'default_password'    => $request->filled('guardian_password') ? null : $defaultPassword,
                'guardian_created_at' => $user->created_at->toDateString(),
                'student_created_at'  => $student->created_at->toDateString(),
                'email_verified_at'   => $user->email_verified_at,
            ]);
        }

        return redirect()->route('guardians.index')
            ->with('success', 'Guardian and student created successfully. Default password has been sent via email.');
    }
}
