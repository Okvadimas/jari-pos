<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('lead_time')->default(1)->after('current_stock')->comment('Lead time in days');
            $table->integer('moq')->default(1)->after('lead_time')->comment('Minimum Order Quantity');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['lead_time', 'moq']);
        });
    }
};
