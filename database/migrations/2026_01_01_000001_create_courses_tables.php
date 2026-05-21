<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('course_categories')->nullOnDelete();
        });

        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('expertise')->nullable();
            $table->text('description')->nullable();
            $table->string('signature_image')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('revenue_share_percent', 5, 2)->default(70.00);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('total_students')->default(0);
            $table->integer('total_courses')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('course_categories')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('promo_video')->nullable();
            $table->enum('type', ['online', 'physical', 'hybrid'])->default('online');
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
            $table->enum('status', ['draft', 'published', 'archived', 'under_review'])->default('draft');
            $table->string('language', 50)->default('English');
            $table->integer('duration_hours')->nullable();
            $table->integer('duration_weeks')->nullable();
            $table->string('location')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->integer('max_students')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('currency', 10)->default('NGN');
            $table->boolean('is_free')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('certificate_enabled')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->text('requirements')->nullable();
            $table->text('what_you_learn')->nullable();
            $table->text('who_is_this_for')->nullable();
            $table->integer('total_students')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_lessons')->default(0);
            $table->integer('total_duration_minutes')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'is_published', 'is_featured']);
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['video', 'text', 'quiz', 'assignment', 'live', 'download'])->default('video');
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_provider', 50)->nullable(); // youtube, vimeo, bunny
            $table->integer('duration_minutes')->nullable();
            $table->string('attachment')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('course_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('course_tag_pivot', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_tag_id')->constrained('course_tags')->cascadeOnDelete();
            $table->primary(['course_id', 'course_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_tag_pivot');
        Schema::dropIfExists('course_tags');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('course_categories');
    }
};
