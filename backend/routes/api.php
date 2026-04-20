<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SummaryController;
use App\Http\Controllers\Api\V1\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:60,1');
        // Guardians cannot self-register; they are created by teacher inside "Create Student"
        // So we remove or disable the public guardian registration endpoint.
        // Route::post('/register/guardian', [AuthController::class, 'registerGuardian'])->middleware('throttle:10,1');
    });

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Students CRUD (restricted to teacher/admin inside controller)
        Route::apiResource('students', StudentController::class)->middleware('check.student.access');

        // Hard delete route (restricted to teacher/admin inside controller)
        Route::delete('/students/{student}/force', [StudentController::class, 'forceDestroy'])
            ->middleware(['check.student.access']);

        Route::prefix('students/{student}')->middleware('check.student.access')->group(function () {
            // Guardians can view progress and summaries of their own student only
            Route::get('/progress', [ProgressController::class, 'index']);
            Route::post('/progress', [ProgressController::class, 'store']); // Teacher/Admin only
            Route::get('/summaries/{week}', [SummaryController::class, 'show']);
        });

        Route::get('/recommendations', [RecommendationController::class, 'index'])
            ->middleware('cache.headers:public;max_age=3600');

        // Regenerate summary (restricted to teacher/admin inside controller)
        Route::post('/students/{student}/summaries/{week}/regenerate', [SummaryController::class, 'regenerate']);
    });
});
