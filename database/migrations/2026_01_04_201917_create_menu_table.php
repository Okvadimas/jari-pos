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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('parent', 50)->default('0');
            $table->string('nama', 100);
            $table->string('icon', 100)->nullable();
            $table->string('url', 255)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('insert_at')->nullable();
            $table->string('insert_by', 100)->nullable();
            $table->timestamp('update_at')->nullable();
            $table->string('update_by', 100)->nullable();

            $table->index('parent');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
