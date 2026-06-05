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
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\InstallmentController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\AdmissionNumberController;
use App\Http\Controllers\Admin\CertificateSettingsController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
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

// ── Route aliases expected by Laravel internals & legacy links ──────────────
// These use different URLs so they don't overwrite the auth.* group names
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::redirect('/logout', '/auth/login');

// ─── Student Dashboard Routes ──────────────────────────────────────────────────

Route::middleware(['auth'])->prefix('dashboard')->name('student.')->group(function () {
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

    // Quiz Routes
    Route::get('/quiz/{quiz}', [StudentQuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/start', [StudentQuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz-attempt/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz-attempt/{attempt}/result', [StudentQuizController::class, 'result'])->name('quiz.result');

    // Assignment Routes
    Route::get('/assignment/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignment.show');
    Route::post('/assignment/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignment.submit');

    // Attendance Routes
    Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');
    Route::get('/attendance/{course}', [StudentAttendanceController::class, 'show'])->name('attendance.show');

    // Admission Routes
    Route::get('/admission', [\App\Http\Controllers\Student\AdmissionController::class, 'index'])->name('admission');
    Route::get('/admission/download', [\App\Http\Controllers\Student\AdmissionController::class, 'downloadSlip'])->name('admission.download');
});

// ─── Enrollment & Payment ─────────────────────────────────────────────────────

Route::middleware(['auth'])->prefix('enroll')->name('enroll.')->group(function () {
    Route::get('/{slug}', [StudentCourseController::class, 'checkout'])->name('checkout');
    Route::post('/{slug}', [StudentCourseController::class, 'initPayment'])->name('payment.init');
    Route::get('/callback/{reference}', [StudentCourseController::class, 'paymentCallback'])->name('payment.callback');
});

// ─── Paystack Webhook (no auth, no CSRF) ──────────────────────────────────────
Route::post('/webhooks/paystack', [StudentCourseController::class, 'paystackWebhook'])
    ->name('webhooks.paystack')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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

    // Enrollments & Admission Management
    Route::resource('enrollments', EnrollmentController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('/enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('enrollments.approve');
    Route::post('/enrollments/{enrollment}/reject', [EnrollmentController::class, 'reject'])->name('enrollments.reject');
    Route::post('/enrollments/{enrollment}/suspend', [EnrollmentController::class, 'suspend'])->name('enrollments.suspend');
    Route::post('/enrollments/{enrollment}/unlock', [EnrollmentController::class, 'unlock'])->name('enrollments.unlock');
    Route::post('/enrollments/{enrollment}/reassign-batch', [EnrollmentController::class, 'reassignBatch'])->name('enrollments.reassign-batch');
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);
    Route::post('/payments/{id}/refund', [PaymentController::class, 'refund'])->name('payments.refund');

    // Blog
    Route::resource('blog/categories', \App\Http\Controllers\Admin\BlogCategoryController::class)->names('blog.categories');
    Route::resource('blog/posts', BlogManagementController::class)->names('blog.posts');
    Route::post('/blog/posts/{id}/publish', [BlogManagementController::class, 'publish'])->name('blog.posts.publish');

    // CMS
    Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
    Route::resource('cms/pages', \App\Http\Controllers\Admin\PageBuilderController::class)->names('cms.pages');
    Route::match(['POST', 'PUT', 'PATCH'], '/cms/hero-banners', [CmsController::class, 'updateHeroBanner'])->name('cms.hero.update');
    Route::delete('/cms/hero-banners/{id}', [CmsController::class, 'destroyHeroBanner'])->name('cms.hero.destroy');
    Route::post('/cms/testimonials', [CmsController::class, 'storeTestimonial'])->name('cms.testimonials.store');
    Route::delete('/cms/testimonials/{id}', [CmsController::class, 'destroyTestimonial'])->name('cms.testimonials.destroy');
    Route::post('/cms/faqs', [CmsController::class, 'storeFaq'])->name('cms.faqs.store');
    Route::delete('/cms/faqs/{id}', [CmsController::class, 'destroyFaq'])->name('cms.faqs.destroy');

    // Media
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Leads & CRM
    Route::resource('leads', LeadController::class);
    Route::get('/newsletter', [LeadController::class, 'newsletter'])->name('newsletter');
    Route::get('/contact-submissions', [LeadController::class, 'contacts'])->name('contacts');
    Route::get('/leads/export', [LeadController::class, 'exportLeads'])->name('leads.export');
    Route::get('/contacts/export', [LeadController::class, 'exportContacts'])->name('contacts.export');
    Route::get('/newsletter/export', [LeadController::class, 'exportNewsletter'])->name('newsletter.export');

    // Testimonials & FAQs
    Route::resource('testimonials', TestimonialController::class);
    Route::post('/testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
    Route::resource('faqs', FaqController::class);

    // Team / Gallery / Services / Announcements CMS
    Route::resource('team', \App\Http\Controllers\Admin\TeamController::class)->except(['create', 'show', 'edit']);
    Route::resource('gallery', \App\Http\Controllers\Admin\GalleryController::class)->except(['create', 'show', 'edit']);
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->except(['create', 'show', 'edit']);
    Route::resource('announcements', AnnouncementController::class)->except(['create', 'show', 'edit']);

    // Users export
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');

    // ─── LMS Extension ────────────────────────────────────────────────────────
    // Quizzes
    Route::get('/courses/{course}/quizzes', [AdminQuizController::class, 'index'])->name('courses.quizzes.index');
    Route::get('/courses/{course}/quizzes/create', [AdminQuizController::class, 'create'])->name('courses.quizzes.create');
    Route::post('/courses/{course}/quizzes', [AdminQuizController::class, 'store'])->name('courses.quizzes.store');
    Route::get('/quizzes/{quiz}/edit', [AdminQuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [AdminQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::post('/quizzes/{quiz}/questions', [AdminQuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::delete('/quiz-questions/{question}', [AdminQuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
    Route::post('/quizzes/{quiz}/reorder', [AdminQuizController::class, 'reorderQuestions'])->name('quizzes.questions.reorder');

    // Assignments
    Route::get('/courses/{course}/assignments', [AdminAssignmentController::class, 'index'])->name('courses.assignments.index');
    Route::get('/courses/{course}/assignments/create', [AdminAssignmentController::class, 'create'])->name('courses.assignments.create');
    Route::post('/courses/{course}/assignments', [AdminAssignmentController::class, 'store'])->name('courses.assignments.store');
    Route::get('/assignments/{assignment}/edit', [AdminAssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [AdminAssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AdminAssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::get('/assignments/{assignment}/submissions', [AdminAssignmentController::class, 'submissions'])->name('assignments.submissions');
    Route::post('/submissions/{submission}/grade', [AdminAssignmentController::class, 'gradeSubmission'])->name('submissions.grade');

    // Batches
    Route::resource('batches', BatchController::class);
    Route::get('/batches/{batch}/enrollments', [BatchController::class, 'enrollments'])->name('batches.enrollments');
    Route::post('/batches/{batch}/students', [BatchController::class, 'addStudent'])->name('batches.students.add');
    Route::delete('/batch-enrollments/{batchEnrollment}', [BatchController::class, 'removeStudent'])->name('batches.students.remove');

    // Attendance
    Route::get('/courses/{course}/attendance', [AdminAttendanceController::class, 'index'])->name('courses.attendance.index');
    Route::get('/courses/{course}/attendance/session/create', [AdminAttendanceController::class, 'createSession'])->name('courses.attendance.session.create');
    Route::post('/courses/{course}/attendance/session', [AdminAttendanceController::class, 'storeSession'])->name('courses.attendance.session.store');
    Route::get('/attendance/{session}/mark', [AdminAttendanceController::class, 'markAttendance'])->name('attendance.mark');
    Route::post('/attendance/{session}/save', [AdminAttendanceController::class, 'saveAttendance'])->name('attendance.save');
    Route::get('/courses/{course}/attendance/report', [AdminAttendanceController::class, 'report'])->name('courses.attendance.report');
    Route::get('/courses/{course}/attendance/settings', [AdminAttendanceController::class, 'settings'])->name('courses.attendance.settings');
    Route::post('/courses/{course}/attendance/settings', [AdminAttendanceController::class, 'saveSettings'])->name('courses.attendance.settings.save');
    Route::delete('/attendance-sessions/{session}', [AdminAttendanceController::class, 'destroy'])->name('attendance.sessions.destroy');

    // Installments
    Route::resource('installments', InstallmentController::class)->except(['edit', 'update']);
    Route::post('/installments/{installmentPlan}/payment', [InstallmentController::class, 'recordPayment'])->name('installments.payment');
    Route::post('/installments/{installmentPlan}/unlock', [InstallmentController::class, 'unlock'])->name('installments.unlock');

    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class)->names('email-templates');
    Route::get('/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
    Route::post('/email-templates/{emailTemplate}/toggle', [EmailTemplateController::class, 'toggleActive'])->name('email-templates.toggle');

    // Admission Numbers
    Route::get('/settings/admission-numbers', [AdmissionNumberController::class, 'settings'])->name('settings.admission-numbers');
    Route::post('/settings/admission-numbers', [AdmissionNumberController::class, 'saveSettings'])->name('settings.admission-numbers.save');
    Route::get('/settings/admission-numbers/list', [AdmissionNumberController::class, 'index'])->name('settings.admission-numbers.list');
    Route::post('/settings/admission-numbers/assign', [AdmissionNumberController::class, 'assign'])->name('settings.admission-numbers.assign');
    Route::post('/settings/admission-numbers/bulk-assign', [AdmissionNumberController::class, 'bulkAssign'])->name('settings.admission-numbers.bulk');

    // Certificate Settings
    Route::get('/certificates', [CertificateSettingsController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/settings', [CertificateSettingsController::class, 'settings'])->name('certificates.settings');
    Route::post('/certificates/settings', [CertificateSettingsController::class, 'saveSettings'])->name('certificates.settings.save');
    Route::post('/certificates/issue', [CertificateSettingsController::class, 'issue'])->name('certificates.issue');
    Route::delete('/certificates/{certificate}', [CertificateSettingsController::class, 'revoke'])->name('certificates.revoke');

    // Support
    Route::resource('support', SupportController::class)->names('support');
    Route::post('/support/{id}/assign', [SupportController::class, 'assign'])->name('support.assign');
    Route::post('/support/{id}/close', [SupportController::class, 'close'])->name('support.close');
    Route::post('/support/{support}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/{group}', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/scripts', [SettingsController::class, 'scripts'])->name('settings.scripts');
    Route::post('/settings/scripts', [SettingsController::class, 'storeScript'])->name('settings.scripts.store');
    Route::delete('/settings/scripts/{id}', [SettingsController::class, 'destroyScript'])->name('settings.scripts.destroy');
    Route::get('/settings/seo', [SettingsController::class, 'seo'])->name('settings.seo');
    Route::post('/settings/seo', [SettingsController::class, 'updateSeo'])->name('settings.seo.update');
    Route::get('/settings/menus', [SettingsController::class, 'menus'])->name('settings.menus');
    Route::post('/settings/menus', [SettingsController::class, 'storeMenu'])->name('settings.menus.store');
    Route::post('/settings/menus/items', [SettingsController::class, 'storeMenuItem'])->name('settings.menus.items.store');
    Route::put('/settings/menus/items/{item}', [SettingsController::class, 'updateMenuItem'])->name('settings.menus.items.update');
    Route::delete('/settings/menus/items/{item}', [SettingsController::class, 'destroyMenuItem'])->name('settings.menus.items.destroy');
    Route::post('/settings/menus/items/reorder', [SettingsController::class, 'reorderMenuItems'])->name('settings.menus.items.reorder');
    Route::post('/settings/integrations', [SettingsController::class, 'updateIntegrations'])->name('settings.integrations');
});

// ─── Public Certificate Verification ──────────────────────────────────────────
Route::get('/verify/{token}', [CertificateSettingsController::class, 'verify'])->name('certificate.verify');

// ─── Public Admission Verification ────────────────────────────────────────────
Route::get('/admission/verify/{number}', [\App\Http\Controllers\Web\AdmissionVerificationController::class, 'verify'])->name('admission.verify.public');

// ─── Dynamic CMS Pages (must be last) ─────────────────────────────────────────
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
