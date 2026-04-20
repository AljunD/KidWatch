<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Guardian;

class AuthController extends Controller  // ← This must extend our custom Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return $this->errorResponse('Account is inactive', 403);
        }

        $token = $user->createToken('kidwatch-api-token', ['role:' . $user->role])->plainTextToken;

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function registerGuardian(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'relationship_to_child' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'guardian',
            'is_active' => true,
        ]);

        Guardian::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'relationship_to_child' => $request->relationship_to_child,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        $token = $user->createToken('kidwatch-guardian-token')->plainTextToken;

        return $this->successResponse([
            'user' => ['id' => $user->id, 'email' => $user->email, 'role' => 'guardian'],
            'token' => $token,
        ], 'Guardian registered successfully', 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Logged out successfully');
    }
}
