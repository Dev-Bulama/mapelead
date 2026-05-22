<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Module 1: LMS — Quiz System ─────────────────────────────────────────
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('pass_score')->default(70); // percentage
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('max_attempts')->default(3);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_answers_after')->default(true);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['single_choice', 'multiple_choice', 'true_false', 'short_answer'])->default('single_choice');
            $table->integer('points')->default(1);
            $table->integer('sort_order')->default(0);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->decimal('score_percent', 5, 2)->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('earned_points')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'quiz_id']);
        });

        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->json('selected_option_ids')->nullable();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('points_earned')->default(0);
            $table->timestamps();
        });

        // ─── Module 1: LMS — Assignment System ───────────────────────────────────
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->longText('instructions')->nullable();
            $table->integer('max_score')->default(100);
            $table->integer('pass_score')->default(60);
            $table->integer('due_days_after_enrollment')->nullable();
            $table->string('allowed_file_types')->default('pdf,doc,docx,zip'); // comma-separated
            $table->integer('max_file_size_mb')->default(10);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('notes')->nullable();
            $table->integer('score')->nullable();
            $table->enum('status', ['pending', 'submitted', 'graded', 'returned', 'resubmit'])->default('pending');
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'assignment_id']);
        });

        // ─── Module 1: LMS — Batch/Cohort System ────────────────────────────────
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('max_students')->nullable();
            $table->integer('current_students')->default(0);
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('batch_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['batch_id', 'enrollment_id']);
        });

        // ─── Module 2: Attendance System ────────────────────────────────────────
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('type', ['online', 'physical', 'hybrid'])->default('online');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['course_id', 'date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->timestamp('check_in_time')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['session_id', 'user_id']);
        });

        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete()->unique();
            $table->integer('minimum_percentage')->default(80);
            $table->boolean('notify_student_below_threshold')->default(true);
            $table->boolean('notify_admin_below_threshold')->default(true);
            $table->integer('notify_threshold')->default(75); // notify when drops below this %
            $table->boolean('block_certificate_below_minimum')->default(true);
            $table->timestamps();
        });

        // ─── Module 3: Installment Payment System ───────────────────────────────
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2);
            $table->integer('installment_count')->default(2);
            $table->integer('grace_period_days')->default(3);
            $table->boolean('auto_lock_on_overdue')->default(true);
            $table->enum('status', ['active', 'completed', 'overdue', 'defaulted', 'cancelled'])->default('active');
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('installment_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('installment_plans')->cascadeOnDelete();
            $table->integer('installment_number');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'paid', 'overdue', 'partially_paid', 'waived'])->default('pending');
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
        });

        // ─── Module 4: Payment Reminders (config) ───────────────────────────────
        Schema::create('payment_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('remind_7_days')->default(true);
            $table->boolean('remind_3_days')->default(true);
            $table->boolean('remind_1_day')->default(true);
            $table->boolean('remind_due_date')->default(true);
            $table->boolean('remind_overdue')->default(true);
            $table->integer('overdue_reminder_interval_days')->default(3);
            $table->boolean('send_email')->default(true);
            $table->boolean('send_in_app')->default(true);
            $table->timestamps();
        });

        // ─── Module 5: Email Template CMS ───────────────────────────────────────
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subject');
            $table->longText('body_html');
            $table->text('body_text')->nullable();
            $table->json('available_variables')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        // ─── Module 7: Admission Number Settings ─────────────────────────────────
        Schema::create('admission_number_settings', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->default('MAP');
            $table->string('separator')->default('/');
            $table->boolean('include_year')->default(true);
            $table->boolean('include_course_code')->default(false);
            $table->integer('digit_length')->default(4);
            $table->boolean('reset_yearly')->default(true);
            $table->integer('last_sequential_number')->default(0);
            $table->integer('last_year')->nullable();
            $table->timestamps();
        });

        // ─── Module 8: Certificate Settings ─────────────────────────────────────
        Schema::create('certificate_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('seal_path')->nullable();
            $table->string('header_text')->default('Certificate of Completion');
            $table->text('body_text')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('background_color')->default('#ffffff');
            $table->string('primary_color')->default('#14215B');
            $table->string('text_color')->default('#1a1a1a');
            $table->enum('layout', ['landscape', 'portrait'])->default('landscape');
            $table->boolean('qr_enabled')->default(true);
            $table->string('organization_name')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_settings');
        Schema::dropIfExists('admission_number_settings');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('payment_reminder_settings');
        Schema::dropIfExists('installment_schedule');
        Schema::dropIfExists('installment_plans');
        Schema::dropIfExists('attendance_settings');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('batch_enrollments');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
