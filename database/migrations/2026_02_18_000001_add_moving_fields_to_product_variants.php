<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('moving_status', 10)->nullable()->after('sku')
                ->comment('fast, medium, slow, dead');
            $table->decimal('moving_score', 5, 4)->nullable()->after('moving_status')
                ->comment('Hybrid score: 0.0000 - 1.0000');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['moving_status', 'moving_score']);
        });
    }
};
