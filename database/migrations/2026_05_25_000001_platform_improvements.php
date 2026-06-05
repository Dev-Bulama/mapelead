<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Make blog_posts.category_id nullable ─────────────────────────────
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->change();
        });

        // ── 2. Add code/type to announcements ───────────────────────────────────
        if (!Schema::hasColumn('announcements', 'type')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->enum('type', ['bar', 'ticker', 'popup'])->default('bar')->after('id');
                $table->string('title', 200)->nullable()->after('type');
            });
        }

        // ── 3. Strict batch code + cohort number ─────────────────────────────────
        // batches.code already exists from original migration

        // ── 4. Team members ───────────────────────────────────────────────────────
        if (!Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('position');
                $table->string('department')->nullable();
                $table->text('bio')->nullable();
                $table->string('photo')->nullable();
                $table->string('email')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('twitter_url')->nullable();
                $table->string('github_url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── 5. Gallery items ─────────────────────────────────────────────────────
        if (!Schema::hasTable('gallery_items')) {
            Schema::create('gallery_items', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('image');
                $table->text('caption')->nullable();
                $table->string('category', 100)->nullable();
                $table->string('alt_text')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── 6. Services ──────────────────────────────────────────────────────────
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('icon', 100)->nullable();
                $table->string('image')->nullable();
                $table->string('color', 20)->default('#14215B');
                $table->string('link_url')->nullable();
                $table->string('link_text', 50)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('team_members');
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable(false)->change();
        });
    }
};
