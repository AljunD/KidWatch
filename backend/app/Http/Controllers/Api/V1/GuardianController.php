<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', 401, ['email or password incorrect']);
        }

        $user = Auth::user();

        if (!$user->is_active || $user->role !== 'guardian') {
            Auth::logout();
            return $this->errorResponse('Unauthorized', 403, ['Only guardians can log in']);
        }

        $token = $user->createToken('guardian-token')->plainTextToken;

        return $this->successResponse([
            'user'  => ['id' => $user->id, 'email' => $user->email, 'role' => $user->role],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Logged out successfully');
    }
}
