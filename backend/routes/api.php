<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GuardianController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SummaryController;

Route::prefix('v1/guardian')->group(function () {
    // Authentication (public)
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Guardian profile
        Route::get('/profile', [GuardianController::class, 'profile']);
        Route::put('/profile', [GuardianController::class, 'updateProfile']);

        // Students linked to guardian (view only)
        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{student}', [StudentController::class, 'show'])
            ->middleware('check.student.access');

        // Progress records (view only)
        Route::get('/students/{student}/progress', [ProgressController::class, 'index'])
            ->middleware('check.student.access');

        // Weekly summaries (view + generate)
        Route::get('/students/{student}/summaries', [SummaryController::class, 'index'])
            ->middleware('check.student.access');
        Route::get('/students/{student}/summaries/{week}', [SummaryController::class, 'show'])
            ->middleware('check.student.access');

        // Summary generation endpoint (guardian can trigger)
        Route::post('/students/{student}/summaries/{week}/generate', [SummaryController::class, 'generate'])
            ->middleware('check.student.access');
    });
});
