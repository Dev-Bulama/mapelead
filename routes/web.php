<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\TicketController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseManagementController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\BlogManagementController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SupportController;
use Illuminate\Support\Facades\Route;

// ─── Public Website Routes ────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:contact');
Route::post('/newsletter/subscribe', [ContactController::class, 'subscribe'])->name('newsletter.subscribe')->middleware('throttle:newsletter');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

// Courses
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [CourseController::class, 'category'])->name('category');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
    Route::post('/{slug}/review', [CourseController::class, 'review'])->name('review')->middleware(['auth', 'throttle:review']);
});

// Blog
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    Route::post('/{slug}/comment', [BlogController::class, 'comment'])->name('comment')->middleware('throttle:comment');
});

// ─── Authentication Routes ─────────────────────────────────────────────────────

Route::middleware('guest')->prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:register');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.post')->middleware('throttle:password-reset');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.post')->middleware('throttle:password-reset');
    Route::get('/verify-otp', [AuthController::class, 'showOtp'])->name('otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.post')->middleware('throttle:otp');

    // Social Auth
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google');
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
});

// ─── Student Dashboard Routes ──────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('student.')->group(function () {
    Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses');
    Route::get('/courses/{slug}/learn', [StudentCourseController::class, 'learn'])->name('learn');
    Route::post('/courses/{slug}/lesson/{lessonId}/complete', [StudentCourseController::class, 'markComplete'])->name('lesson.complete');
    Route::get('/certificates', [StudentDashboardController::class, 'certificates'])->name('certificates');
    Route::get('/certificates/{id}/download', [StudentDashboardController::class, 'downloadCertificate'])->name('certificate.download');
    Route::get('/payments', [StudentDashboardController::class, 'payments'])->name('payments');
    Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::get('/notifications', [StudentDashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [StudentDashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [StudentDashboardController::class, 'markNotificationRead'])->name('notification.read');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
});

// ─── Enrollment & Payment ─────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->prefix('enroll')->name('enroll.')->group(function () {
    Route::get('/{slug}', [StudentCourseController::class, 'checkout'])->name('checkout');
    Route::post('/{slug}', [StudentCourseController::class, 'initPayment'])->name('payment.init');
    Route::get('/callback/{reference}', [StudentCourseController::class, 'paymentCallback'])->name('payment.callback');
});

// ─── Instructor Dashboard Routes ───────────────────────────────────────────────

Route::middleware(['auth', 'role:instructor|admin|super_admin'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('courses', InstructorCourseController::class);
    Route::get('/courses/{id}/analytics', [InstructorDashboardController::class, 'courseAnalytics'])->name('course.analytics');
    Route::get('/students', [InstructorDashboardController::class, 'students'])->name('students');
    Route::get('/earnings', [InstructorDashboardController::class, 'earnings'])->name('earnings');
});

// ─── Admin Dashboard Routes ────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/revenue', [AnalyticsController::class, 'revenue'])->name('analytics.revenue');

    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle');

    // Course Management
    Route::resource('courses', CourseManagementController::class);
    Route::post('/courses/{id}/publish', [CourseManagementController::class, 'publish'])->name('courses.publish');
    Route::post('/courses/{id}/unpublish', [CourseManagementController::class, 'unpublish'])->name('courses.unpublish');
    Route::resource('courses.modules', \App\Http\Controllers\Admin\ModuleController::class)->shallow();
    Route::resource('modules.lessons', \App\Http\Controllers\Admin\LessonController::class)->shallow();

    // Enrollments & Payments
    Route::resource('enrollments', EnrollmentController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);
    Route::post('/payments/{id}/refund', [PaymentController::class, 'refund'])->name('payments.refund');

    // Blog
    Route::resource('blog/categories', \App\Http\Controllers\Admin\BlogCategoryController::class)->names('blog.categories');
    Route::resource('blog/posts', BlogManagementController::class)->names('blog.posts');
    Route::post('/blog/posts/{id}/publish', [BlogManagementController::class, 'publish'])->name('blog.posts.publish');

    // CMS
    Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
    Route::resource('cms/pages', \App\Http\Controllers\Admin\PageBuilderController::class)->names('cms.pages');
    Route::post('/cms/hero-banners', [CmsController::class, 'updateHeroBanner'])->name('cms.hero');
    Route::post('/cms/testimonials', [CmsController::class, 'storeTestimonial'])->name('cms.testimonials.store');
    Route::post('/cms/faqs', [CmsController::class, 'storeFaq'])->name('cms.faqs.store');

    // Media
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Leads & CRM
    Route::resource('leads', LeadController::class);
    Route::get('/newsletter', [LeadController::class, 'newsletter'])->name('newsletter');
    Route::get('/contact-submissions', [LeadController::class, 'contacts'])->name('contacts');

    // Testimonials & FAQs
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('faqs', FaqController::class);

    // Support
    Route::resource('support', SupportController::class)->names('support');
    Route::post('/support/{id}/assign', [SupportController::class, 'assign'])->name('support.assign');
    Route::post('/support/{id}/close', [SupportController::class, 'close'])->name('support.close');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/{group}', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/scripts', [SettingsController::class, 'scripts'])->name('settings.scripts');
    Route::post('/settings/scripts', [SettingsController::class, 'storeScript'])->name('settings.scripts.store');
    Route::delete('/settings/scripts/{id}', [SettingsController::class, 'destroyScript'])->name('settings.scripts.destroy');
    Route::get('/settings/seo', [SettingsController::class, 'seo'])->name('settings.seo');
    Route::post('/settings/seo', [SettingsController::class, 'updateSeo'])->name('settings.seo.update');
    Route::get('/settings/menus', [SettingsController::class, 'menus'])->name('settings.menus');
    Route::post('/settings/menus', [SettingsController::class, 'updateMenus'])->name('settings.menus.update');
});

// ─── Dynamic CMS Pages (must be last) ─────────────────────────────────────────
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
