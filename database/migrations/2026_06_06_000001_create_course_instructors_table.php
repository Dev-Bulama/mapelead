<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained()->cascadeOnDelete();
            $table->enum('session', ['morning', 'afternoon', 'evening']);
            $table->time('session_time')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['course_id', 'session']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_instructors');
    }
};
