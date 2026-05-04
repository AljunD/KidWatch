<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardian;
use App\Models\Log;

class AuthController extends Controller
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
                'entity_id' => null,
                'details' => 'Invalid credentials for '.$request->email,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors'  => ['Email or password incorrect']
            ], 401);
        }

        $user = Auth::user();

        if ($user->role !== 'guardian') {
            Auth::logout();

            Log::create([
                'user_id' => $user->id,
                'action' => 'login_denied',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'details' => 'Non-guardian attempted login',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'errors'  => ['Only guardians can log in']
            ], 403);
        }

        if (is_null($user->email_verified_at)) {
            Auth::logout();

            Log::create([
                'user_id' => $user->id,
                'action' => 'login_denied',
                'entity_type' => 'guardian',
                'entity_id' => $user->id,
                'details' => 'Guardian email not verified',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'errors'  => ['Guardian email is not verified']
            ], 403);
        }

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

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'errors'  => ['Guardian account is inactive or deleted']
            ], 403);
        }

        $token = $user->createToken('guardian-token')->plainTextToken;

        Log::create([
            'user_id' => $user->id,
            'action' => 'login_success',
            'entity_type' => 'guardian',
            'entity_id' => $guardian->id,
            'details' => 'Guardian logged in successfully',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id'                => $user->id,
                    'email'             => $user->email,
                    'role'              => $user->role,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'guardian' => [
                    'id'                 => $guardian->id,
                    'first_name'         => $guardian->first_name,
                    'middle_name'        => $guardian->middle_name,
                    'last_name'          => $guardian->last_name,
                    'relationship_to_child' => $guardian->relationship_to_child,
                    'contact_number'     => $guardian->contact_number,
                    'address'            => $guardian->address,
                ],
                'token'      => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
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

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
