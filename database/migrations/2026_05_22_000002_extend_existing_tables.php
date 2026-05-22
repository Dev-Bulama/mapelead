<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Users: add admission number + portal lock ────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'admission_number')) {
                $table->string('admission_number')->unique()->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'portal_locked')) {
                $table->boolean('portal_locked')->default(false)->after('admission_number');
            }
            if (!Schema::hasColumn('users', 'portal_locked_reason')) {
                $table->string('portal_locked_reason')->nullable()->after('portal_locked');
            }
            if (!Schema::hasColumn('users', 'portal_locked_at')) {
                $table->timestamp('portal_locked_at')->nullable()->after('portal_locked_reason');
            }
        });

        // ─── Enrollments: payment type + access lock ─────────────────────────────
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'payment_type')) {
                $table->enum('payment_type', ['full', 'installment'])->default('full')->after('payment_status');
            }
            if (!Schema::hasColumn('enrollments', 'access_locked')) {
                $table->boolean('access_locked')->default(false)->after('payment_type');
            }
            if (!Schema::hasColumn('enrollments', 'access_locked_at')) {
                $table->timestamp('access_locked_at')->nullable()->after('access_locked');
            }
            if (!Schema::hasColumn('enrollments', 'access_locked_reason')) {
                $table->string('access_locked_reason')->nullable()->after('access_locked_at');
            }
        });

        // ─── Certificates: QR code + verification + eligibility criteria ─────────
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'verification_token')) {
                $table->string('verification_token', 64)->unique()->nullable()->after('certificate_number');
            }
            if (!Schema::hasColumn('certificates', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('certificates', 'attendance_percentage')) {
                $table->decimal('attendance_percentage', 5, 2)->nullable()->after('qr_code_path');
            }
            if (!Schema::hasColumn('certificates', 'quiz_score_average')) {
                $table->decimal('quiz_score_average', 5, 2)->nullable()->after('attendance_percentage');
            }
            if (!Schema::hasColumn('certificates', 'payment_completed')) {
                $table->boolean('payment_completed')->default(false)->after('quiz_score_average');
            }
        });

        // ─── Lessons: sequential locking ─────────────────────────────────────────
        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'require_previous_completion')) {
                $table->boolean('require_previous_completion')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('lessons', 'required_lesson_id')) {
                $table->unsignedBigInteger('required_lesson_id')->nullable()->after('require_previous_completion');
            }
        });

        // ─── Hero Banners: slider settings ───────────────────────────────────────
        Schema::table('hero_banners', function (Blueprint $table) {
            if (!Schema::hasColumn('hero_banners', 'slide_duration_seconds')) {
                $table->integer('slide_duration_seconds')->default(5)->after('is_active');
            }
            if (!Schema::hasColumn('hero_banners', 'transition_effect')) {
                $table->enum('transition_effect', ['fade', 'slide', 'zoom'])->default('slide')->after('slide_duration_seconds');
            }
            if (!Schema::hasColumn('hero_banners', 'text_color')) {
                $table->string('text_color')->default('#ffffff')->after('transition_effect');
            }
        });

        // ─── Payments: link to installment plan ──────────────────────────────────
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'installment_schedule_id')) {
                $table->unsignedBigInteger('installment_schedule_id')->nullable()->after('enrollment_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumnIfExists('installment_schedule_id');
        });
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->dropColumnIfExists('slide_duration_seconds');
            $table->dropColumnIfExists('transition_effect');
            $table->dropColumnIfExists('text_color');
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumnIfExists('require_previous_completion');
            $table->dropColumnIfExists('required_lesson_id');
        });
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumnIfExists('verification_token');
            $table->dropColumnIfExists('qr_code_path');
            $table->dropColumnIfExists('attendance_percentage');
            $table->dropColumnIfExists('quiz_score_average');
            $table->dropColumnIfExists('payment_completed');
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumnIfExists('payment_type');
            $table->dropColumnIfExists('access_locked');
            $table->dropColumnIfExists('access_locked_at');
            $table->dropColumnIfExists('access_locked_reason');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('admission_number');
            $table->dropColumnIfExists('portal_locked');
            $table->dropColumnIfExists('portal_locked_reason');
            $table->dropColumnIfExists('portal_locked_at');
        });
    }
};
