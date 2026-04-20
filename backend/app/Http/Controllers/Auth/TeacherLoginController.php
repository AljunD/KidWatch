<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TeacherLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'teacher' || !Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Access denied.',
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')
            ->with('status', 'You have logged out successfully.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending password reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Handle resetting the password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(str()->random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.form')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
