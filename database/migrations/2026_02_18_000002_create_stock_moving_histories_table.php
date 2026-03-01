<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->date('analysis_date');
            $table->integer('period_days')->default(30);
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('total_variants')->default(0);
            $table->integer('total_fast')->default(0);
            $table->integer('total_medium')->default(0);
            $table->integer('total_slow')->default(0);
            $table->integer('total_dead')->default(0);
            $table->decimal('cogs_balance', 15, 0)->nullable()->comment('Modal kembali (purchase_price × qty terjual)');
            $table->decimal('gross_profit_balance', 15, 0)->nullable()->comment('Keuntungan kotor (revenue - COGS)');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            // Prevent duplicate analysis per company per day
            $table->unique(['company_id', 'analysis_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_stocks');
    }
};
