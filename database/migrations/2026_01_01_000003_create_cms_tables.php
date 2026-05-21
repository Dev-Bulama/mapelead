<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('template', 100)->default('default');
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->string('featured_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('show_in_nav')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_homepage')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->text('schema_markup')->nullable();
            $table->integer('revision')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('section_key', 100);
            $table->string('section_type', 100);
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->json('settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['page_id', 'section_key', 'is_active']);
        });

        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('primary_btn_text')->nullable();
            $table->string('primary_btn_url')->nullable();
            $table->string('secondary_btn_text')->nullable();
            $table->string('secondary_btn_url')->nullable();
            $table->string('badge_text')->nullable();
            $table->json('stats')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->default('general');
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type', 50)->default('text'); // text, boolean, json, image, color
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('group');
        });

        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location', 100)->unique(); // header, footer_1, footer_2, mobile
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('icon')->nullable();
            $table->string('target', 20)->default('_self');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_external')->default(false);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('navigation_items')->nullOnDelete();
        });

        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type', 100);
            $table->string('disk', 50)->default('public');
            $table->string('path');
            $table->string('url');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('folder', 255)->nullable();
            $table->json('conversions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('company')->nullable();
            $table->string('avatar')->nullable();
            $table->text('content');
            $table->tinyInteger('rating')->default(5);
            $table->string('course_name')->nullable();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->longText('answer');
            $table->string('category', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('url')->nullable();
            $table->string('url_text')->nullable();
            $table->string('bg_color', 20)->default('#1a1a2e');
            $table->string('text_color', 20)->default('#ffffff');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissible')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('trigger', 50)->default('exit_intent'); // time_delay, exit_intent, scroll, page_load
            $table->integer('trigger_value')->default(5);
            $table->string('target_pages')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('media_library');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('hero_banners');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};
