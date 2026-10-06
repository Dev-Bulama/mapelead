<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('app_settings')->insert([
            ['key' => 'logo_url',   'value' => null,              'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_name',   'value' => 'MAPELEAD LIMITED', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tagline',    'value' => 'Learn Today, Build Tomorrow', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
