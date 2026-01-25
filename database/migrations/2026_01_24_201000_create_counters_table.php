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
        // Create counters table dengan collation yang konsisten
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('company', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('modul', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('counter_value');

            // Unique constraint untuk kombinasi company dan modul
            $table->unique(['company', 'modul'], 'uniq_company_modul');
        });

        // Create stored procedure generate_kode
        $procedure = <<<SQL
DROP PROCEDURE IF EXISTS generate_kode;
SQL;
        DB::unprepared($procedure);

        $procedure = <<<SQL
CREATE PROCEDURE generate_kode(IN modul_input VARCHAR(5) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci, IN company_input VARCHAR(5) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci)
BEGIN
    -- Declare variabel
    DECLARE v_counter INT DEFAULT NULL;
    
    -- Ambil counter terakhir
    SELECT c.counter_value
    INTO v_counter
    FROM counters c
    WHERE c.company = company_input COLLATE utf8mb4_unicode_ci
      AND c.modul = modul_input COLLATE utf8mb4_unicode_ci
    LIMIT 1;
    
    -- Jika belum ada data
    IF v_counter IS NULL THEN
        SET v_counter = 1;

        INSERT INTO counters (company, modul, counter_value)
        VALUES (company_input, modul_input, v_counter);
    ELSE
        SET v_counter = v_counter + 1;

        UPDATE counters
        SET counter_value = v_counter
        WHERE company = company_input COLLATE utf8mb4_unicode_ci
          AND modul = modul_input COLLATE utf8mb4_unicode_ci;
    END IF;
    
    -- Return kode
    SELECT CONCAT(
        modul_input, '-', 
        company_input, '-', 
        LPAD(v_counter, 5, '0')
    ) AS code;
END
SQL;
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop stored procedure
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_kode');

        // Drop table
        Schema::dropIfExists('counters');
    }
};
