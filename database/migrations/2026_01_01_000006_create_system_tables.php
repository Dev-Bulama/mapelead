<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->default('info'); // info, success, warning, error
            $table->string('channel', 50)->default('database'); // database, email, sms
            $table->boolean('is_read')->default(false);
            $table->string('url')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_read']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'action', 'model_type']);
        });

        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 10);
            $table->string('endpoint');
            $table->integer('status_code');
            $table->float('response_time_ms');
            $table->string('ip_address', 45)->nullable();
            $table->json('request_data')->nullable();
            $table->text('response_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('page_type', 100)->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->text('schema_markup')->nullable();
            $table->boolean('no_index')->default(false);
            $table->boolean('no_follow')->default(false);
            $table->timestamps();
            $table->index(['model_type', 'model_id']);
        });

        Schema::create('script_injections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider', 100)->nullable(); // ga4, meta_pixel, tawkto, chatbase
            $table->enum('location', ['head', 'body_start', 'body_end'])->default('body_end');
            $table->longText('code');
            $table->boolean('is_active')->default(true);
            $table->string('pages')->nullable(); // all, specific
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path');
            $table->string('to_path');
            $table->smallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('from_path');
        });

        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type', 50)->default('string');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('title')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('device', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
            $table->index(['url', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('system_configs');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('script_injections');
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications_log');
    }
};
