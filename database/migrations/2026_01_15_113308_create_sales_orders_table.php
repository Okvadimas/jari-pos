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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 20);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('customer_name');
            $table->date('order_date');
            $table->decimal('total_amount', 15, 0)->comment('Total sebelum diskon manual');
            $table->foreignId('applied_promo_id')->nullable()->constrained('promotions')->nullOnDelete();
            $table->decimal('discount_amount', 15, 0)->default(0)->comment('Diskon dari promo');
            $table->decimal('final_amount', 15, 0)->comment('Total yang dibayar customer');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            $table->index('invoice_number');
            $table->index('order_date');
            $table->index('applied_promo_id');
            $table->index('company_id');
            $table->index('payment_method_id');
            $table->unique(['company_id', 'invoice_number'], 'sales_orders_company_invoice_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
