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
        Schema::create('akses', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('kode_menu', 50);
            $table->string('nama_menu', 100);
            $table->tinyInteger('flag_access')->default(0)->comment('0 = read only, 1 = full access, 9 = no access');
            $table->timestamp('insert_at')->default(now());
            $table->string('insert_by', 100)->default('system');
            $table->timestamp('update_at')->nullable();
            $table->string('update_by', 100)->nullable();

            $table->unique(['role', 'kode_menu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akses');
    }
};
