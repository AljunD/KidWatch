<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardian;
use App\Models\Log;

/**
 * Guardian Controller for mobile authentication and account lifecycle
 */
class GuardianController extends Controller
{
    /**
     * Guardian login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::create([
                'user_id' => null,
                'action' => 'login_failed',
                'entity_type' => 'guardian',
                'details' => 'Invalid credentials for '.$request->email,
            ]);

            return $this->errorResponse('Invalid credentials', 401, ['email or password incorrect']);
        }

        $user = Auth::user();

        // Only guardians can log in
        if ($user->role !== 'guardian') {
            Auth::logout();

            Log::create([
                'user_id' => $user->id,
                'action' => 'login_denied',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'details' => 'Non-guardian attempted login',
            ]);

            return $this->errorResponse('Unauthorized', 403, ['Only guardians can log in']);
        }

        // Check guardian record and trashed_at
        $guardian = Guardian::where('user_id', $user->id)->first();
        if (!$guardian || $guardian->trashed_at !== null) {
            Auth::logout();

            Log::create([
                'user_id' => $user->id,
                'action' => 'login_denied',
                'entity_type' => 'guardian',
                'entity_id' => $guardian?->id,
                'details' => 'Guardian account inactive or trashed',
            ]);

            return $this->errorResponse('Unauthorized', 403, ['Guardian account is inactive or deleted']);
        }

        $token = $user->createToken('guardian-token')->plainTextToken;

        Log::create([
            'user_id' => $user->id,
            'action' => 'login_success',
            'entity_type' => 'guardian',
            'entity_id' => $guardian->id,
            'details' => 'Guardian logged in successfully',
        ]);

        return $this->successResponse([
            'user' => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'guardian' => [
                'id' => $guardian->id,
                'first_name' => $guardian->first_name,
                'last_name'  => $guardian->last_name,
            ],
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Guardian logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        $request->user()->currentAccessToken()->delete();

        Log::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'entity_type' => 'guardian',
            'entity_id' => $user->guardian?->id,
            'details' => 'Guardian logged out',
        ]);

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Guardian profile
     */
    public function profile(Request $request)
    {
        $guardian = $request->user()->guardian()->with('students')->first();

        return $this->successResponse($guardian, 'Guardian profile retrieved successfully');
    }

    /**
     * Update guardian profile
     */
    public function updateProfile(Request $request)
    {
        $guardian = $request->user()->guardian;

        $guardian->update($request->only([
            'first_name','middle_name','last_name','contact_number','address','relationship_to_child'
        ]));

        Log::create([
            'user_id' => $request->user()->id,
            'action' => 'profile_update',
            'entity_type' => 'guardian',
            'entity_id' => $guardian->id,
            'details' => 'Guardian profile updated',
        ]);

        return $this->successResponse($guardian, 'Profile updated successfully');
    }
}
