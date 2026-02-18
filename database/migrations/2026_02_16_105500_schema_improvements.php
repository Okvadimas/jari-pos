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
        // 1. Tambah unit_id FK ke product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('product_id')->constrained('units')->nullOnDelete();
            $table->index('unit_id');
        });

        // 2. Standarisasi decimal(15,0) - Rupiah tanpa desimal
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('total_cost', 15, 0)->comment('Total bayar ke supplier')->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('cost_price_per_item', 15, 0)->comment('Harga modal per item setelah dipecah')->change();
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 0)->comment('Total sebelum diskon manual')->change();
            $table->decimal('total_discount_manual', 15, 0)->default(0)->comment('Diskon dari promo')->change();
            $table->decimal('final_amount', 15, 0)->comment('Total yang dibayar customer')->change();
        });

        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 0)->comment('Harga jual normal saat itu')->change();
            $table->decimal('discount_auto_amount', 15, 0)->default(0)->comment('Potongan harga jika ada promo')->change();
            $table->decimal('subtotal', 15, 0)->comment('(unit_price - discount_auto) * qty')->change();
        });

        // 3. Tambah company_id FK ke units
        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            $table->index('company_id');
        });

        // 4. Tambah company_id FK ke categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            $table->index('company_id');
        });

        // 4. Tambah company_id FK ke payment_methods
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // Rollback payment_methods
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
        });

        // Rollback categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
        });

        // Rollback units
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
        });


        // Rollback sales_order_details
        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->comment('Harga jual normal saat itu')->change();
            $table->decimal('discount_auto_amount', 15, 2)->default(0)->comment('Potongan harga jika ada promo')->change();
            $table->decimal('subtotal', 15, 2)->comment('(unit_price - discount_auto) * qty')->change();
        });

        // Rollback sales_orders
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->comment('Total sebelum diskon manual')->change();
            $table->decimal('total_discount_manual', 15, 2)->default(0)->comment('Diskon dari promo')->change();
            $table->decimal('final_amount', 15, 2)->comment('Total yang dibayar customer')->change();
        });

        // Rollback purchase_details
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('cost_price_per_item', 15, 2)->comment('Harga modal per item setelah dipecah')->change();
        });

        // Rollback purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('total_cost', 15, 2)->comment('Total bayar ke supplier')->change();
        });

        // Rollback product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropIndex(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
