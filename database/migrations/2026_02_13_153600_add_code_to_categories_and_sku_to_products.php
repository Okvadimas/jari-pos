<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom code di categories (3 huruf uppercase, misal: FOD, BVR)
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code', 3)->after('id')->comment('Kode 3 huruf kategori, misal: FOD, BVR');
        });

        // Tambah kolom sku di products
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 20)->nullable()->after('company_id')->comment('SKU format: KAT-NNNN');
            $table->unique('sku');
        });

        // Stored procedure: generate_sku
        // Reuse tabel counters, dengan year=0, month=0 (counter global, tidak per bulan)
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_sku');

        $procedure = <<<SQL
CREATE PROCEDURE generate_sku(
    IN category_code_input VARCHAR(3) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN company_input VARCHAR(5) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_counter INT DEFAULT NULL;
    DECLARE v_exists INT DEFAULT 0;

    -- Check if counter exists with FOR UPDATE lock
    SELECT COUNT(*), COALESCE(MAX(counter_value), 0)
    INTO v_exists, v_counter
    FROM counters
    WHERE company = company_input COLLATE utf8mb4_unicode_ci
      AND modul = category_code_input COLLATE utf8mb4_unicode_ci
      AND year = 0
      AND month = 0
    FOR UPDATE;

    -- If no data exists, create new counter
    IF v_exists = 0 THEN
        SET v_counter = 1;

        INSERT INTO counters (company, modul, year, month, counter_value)
        VALUES (company_input, category_code_input, 0, 0, v_counter);
    ELSE
        SET v_counter = v_counter + 1;

        UPDATE counters
        SET counter_value = v_counter
        WHERE company = company_input COLLATE utf8mb4_unicode_ci
          AND modul = category_code_input COLLATE utf8mb4_unicode_ci
          AND year = 0
          AND month = 0;
    END IF;

    -- Return formatted SKU: KAT-0001
    SELECT CONCAT(
        category_code_input, '-',
        LPAD(v_counter, 4, '0')
    ) AS sku;
END
SQL;
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_sku');

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
