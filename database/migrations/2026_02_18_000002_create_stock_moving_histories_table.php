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
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate analysis per company per day
            $table->unique(['company_id', 'analysis_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_stocks');
    }
};
