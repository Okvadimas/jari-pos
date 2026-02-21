<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. sales_orders: rename total_discount_manual → discount_amount
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->renameColumn('total_discount_manual', 'discount_amount');
        });

        // 2. sales_order_details: rename discount_auto_amount → discount_amount, add invoice_number
        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->renameColumn('discount_auto_amount', 'discount_amount');
        });

        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->string('invoice_number', 20)->nullable()->after('sales_order_id');
            $table->index('invoice_number');
        });

        // 3. Backfill invoice_number from sales_orders
        \DB::statement('
            UPDATE sales_order_details sod
            JOIN sales_orders so ON so.id = sod.sales_order_id
            SET sod.invoice_number = so.invoice_number
        ');
    }

    public function down(): void
    {
        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropColumn('invoice_number');
        });

        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->renameColumn('discount_amount', 'discount_auto_amount');
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->renameColumn('discount_amount', 'total_discount_manual');
        });
    }
};
