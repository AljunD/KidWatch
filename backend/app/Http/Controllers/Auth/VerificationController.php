<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Handle the email verification link.
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        Log::info('User email verified', ['user_id' => $request->user()->id]);

        // Show the success page
        return view('auth.verify-success');
    }

    /**
     * Resend the verification email.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        return back()->with('success', 'A new verification link has been sent to your email address.');
    }
}
