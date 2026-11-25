<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->longText('body_html')->nullable()->after('message');
            $table->string('media_url')->nullable()->after('body_html');
            $table->string('media_type')->nullable()->after('media_url'); // image|video|youtube|none
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['body_html', 'media_url', 'media_type']);
        });
    }
};
