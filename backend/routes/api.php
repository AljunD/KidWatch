<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SummaryController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Guardian-only endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        // List all students (no student ID required)
        Route::get('/students', [StudentController::class, 'index']);

        // Routes that require a student ID
        Route::middleware(['check.student.access'])->group(function () {
            Route::get('/students/{student}', [StudentController::class, 'show']);
            Route::get('/students/{student}/progress', [ProgressController::class, 'index']);
            Route::get('/students/{student}/weeks/{week}/summary', [SummaryController::class, 'show']);
        });
    });
});
