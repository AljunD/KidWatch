<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Log;
use App\Http\Resources\StudentResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::create([
                'user_id'     => null,
                'action'      => 'login_failed',
                'entity_type' => 'guardian',
                'entity_id'   => null,
                'details'     => 'Invalid credentials for '.$request->email,
            ]);
            return $this->errorResponse('Invalid credentials', 401, ['Email or password incorrect']);
        }

        $user = Auth::user();

        if ($user->role !== 'guardian') {
            Auth::logout();
            Log::create([
                'user_id'     => $user->id,
                'action'      => 'login_denied',
                'entity_type' => 'user',
                'entity_id'   => $user->id,
                'details'     => 'Non-guardian attempted login',
            ]);
            return $this->errorResponse('Unauthorized', 403, ['Only guardians can log in']);
        }

        $guardian = Guardian::where('user_id', $user->id)->first();
        if (!$guardian || $guardian->trashed_at !== null) {
            Auth::logout();
            Log::create([
                'user_id'     => $user->id,
                'action'      => 'login_denied',
                'entity_type' => 'guardian',
                'entity_id'   => $guardian?->id,
                'details'     => 'Guardian account inactive or trashed',
            ]);
            return $this->errorResponse('Unauthorized', 403, ['Guardian account is inactive or deleted']);
        }

        $token = $user->createToken('guardian-token')->plainTextToken;

        Log::create([
            'user_id'     => $user->id,
            'action'      => 'login_success',
            'entity_type' => 'guardian',
            'entity_id'   => $guardian->id,
            'details'     => 'Guardian logged in successfully',
        ]);

        return $this->successResponse([
            'user' => [
                'id'                => $user->id,
                'email'             => $user->email,
                'role'              => $user->role,
                'email_verified_at' => $user->email_verified_at,
            ],
            'guardian' => [
                'id'                   => $guardian->id,
                'first_name'           => $guardian->first_name,
                'middle_name'          => $guardian->middle_name,
                'last_name'            => $guardian->last_name,
                'relationship_to_child'=> $guardian->relationship_to_child,
                'contact_number'       => $guardian->contact_number,
                'address'              => $guardian->address,
                'email'                => $user->email,
            ],
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        Log::create([
            'user_id'     => $user->id,
            'action'      => 'logout',
            'entity_type' => 'guardian',
            'entity_id'   => $user->guardian?->id,
            'details'     => 'Guardian logged out',
        ]);

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function profile(Request $request): JsonResponse
    {
        $guardian = $request->user()
            ->guardian()
            ->with(['students', 'user'])
            ->first();

        if (!$guardian) {
            return $this->notFoundResponse('Guardian');
        }

        return $this->successResponse($guardian, 'Guardian profile retrieved successfully');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $guardian = $request->user()->guardian;

        if (!$guardian) {
            return $this->notFoundResponse('Guardian');
        }

        $guardian->update($request->only([
            'first_name','middle_name','last_name','contact_number','address','relationship_to_child'
        ]));

        Log::create([
            'user_id'     => $request->user()->id,
            'action'      => 'profile_update',
            'entity_type' => 'guardian',
            'entity_id'   => $guardian->id,
            'details'     => 'Guardian profile updated',
        ]);

        return $this->successResponse($guardian->load('user'), 'Profile updated successfully'); // ✅ include user relation
    }

    public function students(): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        $students = Student::where('guardian_id', $guardian->id)
            ->whereNull('trashed_at')
            ->paginate(10);

        if ($students->isEmpty()) {
            return $this->notFoundResponse('Students');
        }

        return $this->successResponse(
            StudentResource::collection($students),
            'Students retrieved successfully',
            200,
            [
                'pagination' => [
                    'current_page' => $students->currentPage(),
                    'last_page'    => $students->lastPage(),
                    'per_page'     => $students->perPage(),
                    'total'        => $students->total(),
                ]
            ]
        );
    }

    public function studentDetail(Student $student): JsonResponse
    {
        $guardian = Auth::user()->guardian;

        if (!$guardian || $guardian->trashed_at !== null) {
            return $this->unauthorizedResponse('Guardian account is inactive or deleted');
        }

        if ($student->guardian_id !== $guardian->id || $student->trashed_at !== null) {
            return $this->unauthorizedResponse('You cannot access this student');
        }

        $student->load([
            'guardian.user', // ✅ include guardian’s user relation
            'progressRecords' => fn($query) => $query->whereNull('trashed_at')->with('week'),
            'weeklySummaries' => fn($query) => $query->whereNull('trashed_at')->with('week'),
        ]);

        return $this->successResponse(
            new StudentResource($student),
            'Student details retrieved successfully'
        );
    }
}

