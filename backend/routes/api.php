<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GuardianController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SummaryController;

Route::prefix('api/v1/guardian')->group(function () {
    // Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Guardian profile
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [GuardianController::class, 'profile']);
        Route::put('/profile', [GuardianController::class, 'updateProfile']);

        // Students linked to guardian
        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
        Route::post('/students/{id}/restore', [StudentController::class, 'restore']);

        // Progress records
        Route::get('/students/{id}/progress', [ProgressController::class, 'index']);
        Route::get('/students/{id}/progress/week/{weekId}', [ProgressController::class, 'show']);

        // Weekly summaries
        Route::get('/students/{id}/summaries', [SummaryController::class, 'index']);
        Route::get('/students/{id}/summaries/{weekId}', [SummaryController::class, 'show']);
    });
});
