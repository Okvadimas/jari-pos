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
        Schema::create('app_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number', 20);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('plan_name')->comment('Starter, Pro, Enterprise, etc.');
            $table->integer('duration_months')->default(1);
            $table->boolean('is_renewal')->default(false);
            $table->decimal('original_amount', 15, 0)->comment('Harga asli sebelum diskon');
            $table->decimal('discount_amount', 15, 0)->default(0)->comment('Potongan kupon diskon');
            $table->decimal('affiliate_discount_amount', 15, 0)->default(0)->comment('Potongan affiliate');
            $table->decimal('final_amount', 15, 0)->comment('Harga final yang dibayar');
            $table->string('affiliate_coupon_code')->nullable();
            $table->string('voucher_code')->nullable();
            $table->string('status')->default('pending')->comment('pending, confirmed, cancelled');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->date('sale_date');
            $table->text('reference_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            $table->index('sale_number');
            $table->index('sale_date');
            $table->index('company_id');
            $table->index('status');
            $table->index('affiliate_coupon_code');
            $table->unique(['company_id', 'sale_number'], 'app_sales_company_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_sales');
    }
};
