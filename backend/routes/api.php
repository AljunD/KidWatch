<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SummaryController;
use App\Http\Controllers\Api\V1\ProgressHistoryController;

Route::prefix('v1/guardian')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/profile', [ProfileController::class, 'profile']);
        Route::put('/profile', [ProfileController::class, 'updateProfile']);

        Route::get('/students', [ProfileController::class, 'students']);
        Route::get('/students/{student}', [ProfileController::class, 'studentDetail'])
            ->middleware('check.student.access');

        Route::get('/students/{student}/progress', [ProgressController::class, 'index'])
            ->middleware('check.student.access');

        Route::get('/students/{student}/progress-history', [ProgressHistoryController::class, 'index'])
            ->middleware('check.student.access');

        Route::get('/students/{student}/summaries', [SummaryController::class, 'index'])
            ->middleware('check.student.access');
        Route::get('/students/{student}/summaries/{week}', [SummaryController::class, 'show'])
            ->middleware('check.student.access');

        Route::post('/students/{student}/summaries/{week}/generate', [SummaryController::class, 'generate'])
            ->middleware('check.student.access');
    });
});
