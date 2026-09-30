<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\AssignmentApiController;
use App\Http\Controllers\Api\V1\CertificateApiController;
use App\Http\Controllers\Api\V1\CourseApiController;
use App\Http\Controllers\Api\V1\EnrollmentApiController;
use App\Http\Controllers\Api\V1\LessonApiController;
use App\Http\Controllers\Api\V1\NotificationApiController;
use App\Http\Controllers\Api\V1\PaymentApiController;
use App\Http\Controllers\Api\V1\ProfileApiController;
use App\Http\Controllers\Api\V1\ProgressApiController;
use App\Http\Controllers\Api\V1\QuizApiController;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public: Course discovery ───────────────────────────────────────────────
    Route::get('/courses',           [CourseApiController::class, 'index']);
    Route::get('/courses/featured',  [CourseApiController::class, 'featured']);
    Route::get('/courses/search',    [CourseApiController::class, 'search']);
    Route::get('/courses/{slug}',    [CourseApiController::class, 'show']);
    Route::get('/categories',        [CourseApiController::class, 'categories']);

    // ── Public: Certificate verification ──────────────────────────────────────
    Route::get('/certificates/verify/{token}', [CertificateApiController::class, 'verify']);

    // ── Public: Paystack webhook ───────────────────────────────────────────────
    Route::post('/webhooks/paystack', [PaymentApiController::class, 'webhook']);

    // ── Auth ───────────────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthApiController::class, 'login']);
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/logout',   [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
    });

    // ── Protected (Sanctum token required) ────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Me / Auth
        Route::get('/me',                          [AuthApiController::class, 'me']);
        Route::post('/auth/resend-verification',   [AuthApiController::class, 'resendVerification']);
        Route::post('/auth/change-password',       [AuthApiController::class, 'changePassword']);

        // Profile
        Route::get('/profile',              [ProfileApiController::class, 'show']);
        Route::put('/profile',              [ProfileApiController::class, 'update']);
        Route::post('/profile/avatar',      [ProfileApiController::class, 'uploadAvatar']);
        Route::get('/profile/stats',        [ProfileApiController::class, 'learningStats']);

        // Enrollments & Payments
        Route::get('/my-courses',                        [EnrollmentApiController::class, 'index']);
        Route::get('/my-courses/{enrollmentId}',         [EnrollmentApiController::class, 'show']);
        Route::post('/enroll/{courseId}',                [EnrollmentApiController::class, 'enroll']);
        Route::post('/payment/verify',                   [EnrollmentApiController::class, 'paymentVerify']);
        Route::get('/payment/history',                   [PaymentApiController::class, 'history']);

        // Lessons
        Route::get('/lessons/{lessonId}',                 [LessonApiController::class, 'show']);
        Route::post('/lessons/{lessonId}/complete',       [LessonApiController::class, 'complete']);
        Route::post('/lessons/{lessonId}/progress',       [LessonApiController::class, 'updateProgress']);
        Route::post('/lessons/{lessonId}/bookmark',       [LessonApiController::class, 'toggleBookmark']);
        Route::get('/bookmarks',                          [LessonApiController::class, 'getBookmarks']);
        Route::post('/lessons/{lessonId}/note',           [LessonApiController::class, 'saveNote']);
        Route::delete('/lessons/{lessonId}/note',         [LessonApiController::class, 'deleteNote']);
        Route::get('/notes',                              [LessonApiController::class, 'getNotes']);

        // Quizzes
        Route::get('/quizzes/{quizId}',           [QuizApiController::class, 'show']);
        Route::post('/quizzes/{quizId}/submit',   [QuizApiController::class, 'submit']);
        Route::get('/quizzes/{quizId}/attempts',  [QuizApiController::class, 'attempts']);

        // Assignments
        Route::get('/courses/{courseId}/assignments',     [AssignmentApiController::class, 'index']);
        Route::get('/assignments/{assignmentId}',         [AssignmentApiController::class, 'show']);
        Route::post('/assignments/{assignmentId}/submit', [AssignmentApiController::class, 'submit']);

        // Certificates
        Route::get('/certificates',                              [CertificateApiController::class, 'index']);
        Route::get('/certificates/{certificateId}',              [CertificateApiController::class, 'show']);
        Route::get('/courses/{courseId}/certificate-eligibility', [CertificateApiController::class, 'checkEligibility']);

        // Notifications
        Route::get('/notifications',               [NotificationApiController::class, 'index']);
        Route::get('/notifications/unread-count',  [NotificationApiController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read',    [NotificationApiController::class, 'markRead']);
        Route::post('/notifications/read-all',     [NotificationApiController::class, 'markAllRead']);

        // Progress & Streaks
        Route::get('/courses/{courseId}/progress',   [ProgressApiController::class, 'courseProgress']);
        Route::post('/streak',                        [ProgressApiController::class, 'trackStreak']);
        Route::get('/streak',                         [ProgressApiController::class, 'streaks']);
    });
});
