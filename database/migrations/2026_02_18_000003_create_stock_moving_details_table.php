<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_stock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_stock_id')->constrained('recommendation_stocks')->cascadeOnDelete();
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('total_qty_sold')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('avg_daily_sales', 10, 4)->default(0);
            $table->decimal('norm_qty', 6, 4)->default(0);
            $table->decimal('norm_revenue', 6, 4)->default(0);
            $table->decimal('score', 6, 4)->default(0);
            $table->string('moving_status', 10)->default('dead');
            $table->integer('current_stock')->default(0);
            $table->integer('lead_time')->default(0)->comment('Snapshot lead time (days)');
            $table->decimal('purchase_price', 15, 0)->default(0)->comment('Snapshot purchase price');
            $table->decimal('sell_price', 15, 0)->default(0)->comment('Snapshot sell price');
            $table->integer('safety_stock')->default(0)->comment('ceil(avg_daily_sales × lead_time)');
            $table->integer('moq')->default(1)->comment('Snapshot minimum order qty');
            $table->timestamps();
            $table->softDeletes();

            $table->index('recommendation_stock_id');
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_stock_details');
    }
};
