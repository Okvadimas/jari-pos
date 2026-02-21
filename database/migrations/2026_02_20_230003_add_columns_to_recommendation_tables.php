<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Header: recommendation_stocks
        Schema::table('recommendation_stocks', function (Blueprint $table) {
            $table->decimal('cogs_balance', 15, 0)->nullable()->after('total_dead')->comment('Modal kembali (purchase_price × qty terjual)');
            $table->decimal('gross_profit_balance', 15, 0)->nullable()->after('cogs_balance')->comment('Keuntungan kotor (revenue - COGS)');
        });

        // Details: recommendation_stock_details
        Schema::table('recommendation_stock_details', function (Blueprint $table) {
            $table->integer('lead_time')->default(0)->after('current_stock')->comment('Snapshot lead time (days)');
            $table->decimal('purchase_price', 15, 0)->default(0)->after('lead_time')->comment('Snapshot purchase price');
            $table->decimal('sell_price', 15, 0)->default(0)->after('purchase_price')->comment('Snapshot sell price');
            $table->integer('safety_stock')->default(0)->after('sell_price')->comment('ceil(avg_daily_sales × lead_time)');
            $table->integer('moq')->default(1)->after('safety_stock')->comment('Snapshot minimum order qty');
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_stocks', function (Blueprint $table) {
            $table->dropColumn(['cogs_balance', 'gross_profit_balance']);
        });

        Schema::table('recommendation_stock_details', function (Blueprint $table) {
            $table->dropColumn(['lead_time', 'purchase_price', 'sell_price', 'safety_stock', 'moq']);
        });
    }
};
