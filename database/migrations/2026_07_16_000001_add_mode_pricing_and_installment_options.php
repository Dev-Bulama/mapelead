<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('price_online', 12, 2)->nullable()->after('discount_price');
            $table->decimal('price_physical_monthly', 12, 2)->nullable()->after('price_online');
            $table->decimal('price_physical_quarterly', 12, 2)->nullable()->after('price_physical_monthly');
            $table->json('installment_options')->nullable()->after('price_physical_quarterly');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['price_online', 'price_physical_monthly', 'price_physical_quarterly', 'installment_options']);
        });
    }
};
