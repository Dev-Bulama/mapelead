<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('source', 100)->nullable(); // website, social, referral, ad
            $table->string('source_page')->nullable();
            $table->string('campaign')->nullable();
            $table->string('interest')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'enrolled', 'lost'])->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_contacted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['email', 'status', 'source']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('source', 100)->nullable();
            $table->enum('status', ['subscribed', 'unsubscribed', 'bounced'])->default('subscribed');
            $table->string('token', 100)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('form_type', 50)->default('contact'); // contact, support, enrollment_inquiry
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'on_hold'])->default('open');
            $table->string('category', 100)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('contact_forms');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('leads');
    }
};
