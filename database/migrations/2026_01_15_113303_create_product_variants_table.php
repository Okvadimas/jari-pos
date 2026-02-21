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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name')->comment('Misal: 500gr, 250gr, atau Eceran');
            $table->string('sku')->unique();
            $table->integer('current_stock')->default(0);
            $table->integer('lead_time')->default(1)->comment('Lead time in days');
            $table->integer('moq')->default(1)->comment('Minimum Order Quantity');
            $table->string('moving_status', 10)->nullable()->comment('fast, medium, slow, dead');
            $table->decimal('moving_score', 5, 4)->nullable()->comment('Hybrid score: 0.0000 - 1.0000');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            $table->index('product_id');
            $table->index('unit_id');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
