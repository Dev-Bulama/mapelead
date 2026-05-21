<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\CourseApiController;
use App\Http\Controllers\Api\V1\EnrollmentApiController;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public API
    Route::get('/courses', [CourseApiController::class, 'index']);
    Route::get('/courses/{slug}', [CourseApiController::class, 'show']);
    Route::get('/categories', [CourseApiController::class, 'categories']);

    // Auth API
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthApiController::class, 'login']);
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
    });

    // Protected API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::get('/my-courses', [EnrollmentApiController::class, 'index']);
        Route::post('/enroll/{courseId}', [EnrollmentApiController::class, 'enroll']);
    });
});
