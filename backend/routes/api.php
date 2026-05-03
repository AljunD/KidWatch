<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GuardianController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SummaryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Guardian (mobile) endpoints — view only, plus summary generation.
| Teachers remain web-only via web.php.
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1/guardian')->group(function () {
    // Authentication
    Route::post('/login', [GuardianController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [GuardianController::class, 'logout']);

        // Guardian profile
        Route::get('/profile', [GuardianController::class, 'profile']);
        Route::put('/profile', [GuardianController::class, 'updateProfile']);

        // Students linked to guardian (view only)
        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{student}', [StudentController::class, 'show']);

        // Progress records (view only)
        Route::get('/students/{student}/progress', [ProgressController::class, 'index']);

        // Weekly summaries (view + generate)
        Route::get('/students/{student}/summaries', [SummaryController::class, 'index']);
        Route::get('/students/{student}/summaries/{week}', [SummaryController::class, 'show']);

        // Summary generation endpoint (guardian can trigger)
        Route::post('/students/{student}/summaries/{week}/generate', [SummaryController::class, 'generate']);
    });
});
