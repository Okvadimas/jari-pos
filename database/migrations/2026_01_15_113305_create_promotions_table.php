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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_value', 15, 2);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->comment('Promo berlaku satu kategori');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->comment('Promo berlaku satu produk (semua varian)');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete()->comment('Promo spesifik hanya untuk varian tertentu');
            $table->decimal('min_order_amount', 15, 2)->default(0);
            $table->integer('priority')->default(1)->comment('Semakin kecil semakin prioritas');
            $table->tinyInteger('status')->default(1)->comment('1 = active, 0 = inactive');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            $table->index('category_id');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
