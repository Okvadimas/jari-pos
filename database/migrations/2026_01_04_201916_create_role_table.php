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
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 50)->unique();
            $table->timestamp('insert_at')->default(now());
            $table->string('insert_by', 100)->default('system');
            $table->timestamp('update_at')->nullable();
            $table->string('update_by', 100)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
