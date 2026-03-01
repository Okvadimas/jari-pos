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
        Schema::table('recommendation_stocks', function (Blueprint $table) {
            $table->decimal('total_estimated_nominal', 15, 2)->nullable()->after('gross_profit_balance');
        });

        Schema::table('recommendation_stock_details', function (Blueprint $table) {
            $table->integer('qty_restock')->nullable()->after('safety_stock')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recommendation_stocks', function (Blueprint $table) {
            $table->dropColumn('total_estimated_nominal');
        });

        Schema::table('recommendation_stock_details', function (Blueprint $table) {
            $table->dropColumn('qty_restock');
        });
    }
};
