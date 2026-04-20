<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TeacherLoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Middleware\TeacherAuthMiddleware;
use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgressController; // Added this

// Authentication (CSP + rate limiting)
Route::middleware([ContentSecurityPolicy::class])->group(function () {
    // Show login form (GET)
    Route::get('/login', [TeacherLoginController::class, 'showLoginForm'])->name('login.form');

    // Handle login submission (POST)
    Route::post('/login', [TeacherLoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login');

    Route::post('/logout', [TeacherLoginController::class, 'logout'])->name('logout');

    // Forgot Password
    Route::get('/forgot-password', [TeacherLoginController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('/forgot-password', [TeacherLoginController::class, 'sendResetLink'])
        ->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', function ($token) {
        return view('reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', [TeacherLoginController::class, 'resetPassword'])
        ->name('password.update');
});

// Email Verification Routes
Route::middleware([ContentSecurityPolicy::class])->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['auth', 'signed'])
        ->name('verification.verify');

    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.resend');
});

// Dashboard (protected + CSP + verified)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([TeacherAuthMiddleware::class, ContentSecurityPolicy::class, 'verified'])
    ->name('dashboard');

// Students – Protected + Rate Limited + verified
Route::middleware([TeacherAuthMiddleware::class, 'throttle:60,1', 'verified'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students');
    Route::post('/students/store-with-guardian', [StudentController::class, 'storeWithGuardian'])->name('students.storeWithGuardian');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Trash routes
    Route::get('/students/trash', [StudentController::class, 'trash'])->name('students.trash');
    Route::post('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.forceDelete');
});

// Progress Tracking Routes (Protected + CSP + verified)
Route::middleware([TeacherAuthMiddleware::class, ContentSecurityPolicy::class, 'verified'])->group(function () {
    // List & Form
    Route::get('/progress', [ProgressController::class, 'index'])->name('students.progress');

    // Store Progress
    Route::post('/progress', [ProgressController::class, 'store'])->name('students.progress.store');

    // Update Progress
    Route::put('/progress/{progressRecord}', [ProgressController::class, 'update'])->name('students.progress.update');

    // Delete Progress
    Route::delete('/progress/{progressRecord}', [ProgressController::class, 'destroy'])->name('students.progress.destroy');

    // Weekly Summary
    Route::get('/progress/{student}/{week}/summary', [ProgressController::class, 'summary'])->name('students.progress.summary');
});
