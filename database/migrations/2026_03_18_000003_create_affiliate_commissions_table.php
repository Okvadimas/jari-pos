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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->string('commission_number', 20);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('app_sale_id')->constrained('app_sales')->cascadeOnDelete();
            $table->string('affiliate_name');
            $table->string('affiliate_coupon_code');
            $table->decimal('sale_amount', 15, 0)->comment('Harga jual setelah diskon affiliate');
            $table->decimal('commission_rate', 5, 2)->comment('Persentase komisi (20 untuk baru, 10 untuk perpanjangan)');
            $table->decimal('commission_amount', 15, 0)->comment('Nominal komisi');
            $table->string('status')->default('pending')->comment('pending, paid, cancelled');
            $table->date('paid_date')->nullable();
            $table->text('reference_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            $table->index('commission_number');
            $table->index('company_id');
            $table->index('app_sale_id');
            $table->index('status');
            $table->unique(['company_id', 'commission_number'], 'affiliate_commissions_company_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
