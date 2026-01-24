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
        Schema::create('stock_daily_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->date('date')->comment('Tanggal stok (YYYY-MM-DD)');
            $table->integer('opening_stock')->default(0)->comment('Stok awal hari (diambil dari closing_stock kemarin)');
            $table->integer('in_stock')->default(0)->comment('Total barang masuk dari purchase hari ini');
            $table->integer('out_stock')->default(0)->comment('Total barang keluar dari sales hari ini');
            $table->integer('adjustment_stock')->default(0)->comment('Perubahan manual (barang rusak/hilang) hari ini');
            $table->integer('closing_stock')->comment('Stok akhir hari ini: (opening + in + adj) - out');
            $table->tinyInteger('status')->default(1)->comment('1 = aktif, 0 = non aktif');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            // Unique constraint: satu varian hanya boleh punya satu record per tanggal
            $table->unique(['product_variant_id', 'date']);
            $table->index('date');
            $table->index('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_daily_balances');
    }
};
