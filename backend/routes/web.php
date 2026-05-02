<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TeacherLoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Middleware\TeacherAuthMiddleware;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\WeekController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\TrashController; // ✅ Unified TrashController
use App\Http\Controllers\LogController;    // ✅ Unified LogController

// Swagger docs
Route::view('/api/swagger', 'swagger');
Route::get('/api/docs', fn() => response()->file(base_path('docs/openapi.yaml')));

// Teacher/Admin Authentication
Route::get('/login', [TeacherLoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [TeacherLoginController::class, 'login'])->middleware('throttle:5,1')->name('login');
Route::post('/logout', [TeacherLoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [TeacherLoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [TeacherLoginController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', fn($token) => view('reset-password', ['token' => $token]))->name('password.reset');
Route::post('/reset-password', [TeacherLoginController::class, 'resetPassword'])->name('password.update');

// Email Verification
Route::get('/email/verify', [VerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['auth','signed'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware(['auth','throttle:6,1'])->name('verification.resend');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([TeacherAuthMiddleware::class, 'verified'])
    ->name('dashboard');

// Students + Guardians
Route::middleware([TeacherAuthMiddleware::class,'throttle:60,1','verified'])->group(function () {
    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/{id}/trash', [StudentController::class, 'trash'])->name('students.trash');
    Route::post('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.forceDelete');

    // Guardians
    Route::get('/guardians', [GuardianController::class, 'index'])->name('guardians.index');
    Route::get('/guardians/create', [GuardianController::class, 'create'])->name('guardians.create');
    Route::post('/guardians', [GuardianController::class, 'store'])->name('guardians.store');
    Route::get('/guardians/{guardian}/edit', [GuardianController::class, 'edit'])->name('guardians.edit');
    Route::put('/guardians/{guardian}', [GuardianController::class, 'update'])->name('guardians.update');
    Route::delete('/guardians/{guardian}', [GuardianController::class, 'destroy'])->name('guardians.destroy');

    // Guardian trash routes
    Route::get('/guardians/trash', [GuardianController::class, 'trash'])->name('guardians.trash');
    Route::post('/guardians/{id}/restore', [GuardianController::class, 'restore'])->name('guardians.restore');
    Route::delete('/guardians/{id}/force-delete', [GuardianController::class, 'forceDelete'])->name('guardians.forceDelete');

    // Guardian-linked student management
    Route::get('/guardians/{guardian}/students/create', [GuardianController::class, 'createStudent'])->name('guardians.students.create');
    Route::post('/guardians/{guardian}/students', [GuardianController::class, 'storeStudent'])->name('guardians.students.store');

    // Combined Guardian + Student workflow
    Route::post('/guardians/store-with-student', [GuardianController::class, 'storeWithStudent'])->name('guardians.storeWithStudent');
});

// Progress Tracking
Route::middleware([TeacherAuthMiddleware::class, 'verified'])->group(function () {
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
    Route::get('/progress/create', [ProgressController::class, 'create'])->name('progress.create');
    Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');

    // Edit all subjects for a student/week
    Route::get('/progress/{student}/{week}/edit', [ProgressController::class, 'edit'])->name('progress.edit');
    // Update a single subject record
    Route::put('/progress/{id}', [ProgressController::class, 'update'])->name('progress.update');

    Route::delete('/progress/{progressRecord}', [ProgressController::class, 'destroy'])->name('progress.destroy');

    Route::get('/progress/{student_id}/{week_id}/view', [ProgressController::class, 'view'])->name('progress.view');
    Route::get('/progress/view-all', [ProgressController::class, 'viewAll'])->name('progress.viewAll');
    Route::get('/progress/{student}/{week}/summary', [ProgressController::class, 'summary'])->name('progress.summary');
    Route::post('/progress/{student}/{week}/recommendation', [ProgressController::class, 'generateRecommendation'])->name('progress.generateRecommendation');

    // Weeks
    Route::get('/weeks', [WeekController::class, 'index'])->name('weeks.index');
    Route::post('/weeks', [WeekController::class, 'store'])->name('weeks.store');
});

// ✅ Unified TrashController + Logs routes
Route::middleware([TeacherAuthMiddleware::class,'verified'])->group(function () {
    // Trash
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{type}/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('trash.forceDelete');

    // Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});
