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
        // Create counters table
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('company', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('modul', 5)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('year');
            $table->integer('month');
            $table->integer('counter_value');

            // Unique constraint for company, modul, year, month combination
            $table->unique(['company', 'modul', 'year', 'month'], 'uniq_company_modul_year_month');
        });

        // Create stored procedure with FOR UPDATE locking
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_transaction_number');

        $procedure = <<<SQL
CREATE PROCEDURE generate_transaction_number(
    IN prefix_input VARCHAR(5) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN company_input VARCHAR(5) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN year_input INT,
    IN month_input INT
)
BEGIN
    DECLARE v_counter INT DEFAULT NULL;
    DECLARE v_exists INT DEFAULT 0;
    
    -- Check if counter exists with FOR UPDATE lock (transaction managed by caller)
    SELECT COUNT(*), COALESCE(MAX(counter_value), 0)
    INTO v_exists, v_counter
    FROM counters
    WHERE company = company_input COLLATE utf8mb4_unicode_ci
      AND modul = prefix_input COLLATE utf8mb4_unicode_ci
      AND year = year_input
      AND month = month_input
    FOR UPDATE;
    
    -- If no data exists, create new counter
    IF v_exists = 0 THEN
        SET v_counter = 1;

        INSERT INTO counters (company, modul, year, month, counter_value)
        VALUES (company_input, prefix_input, year_input, month_input, v_counter);
    ELSE
        SET v_counter = v_counter + 1;

        UPDATE counters
        SET counter_value = v_counter
        WHERE company = company_input COLLATE utf8mb4_unicode_ci
          AND modul = prefix_input COLLATE utf8mb4_unicode_ci
          AND year = year_input
          AND month = month_input;
    END IF;
    
    -- Return formatted transaction number: PREFIX/YYYY/MM/0001
    SELECT CONCAT(
        prefix_input, '/',
        LPAD(year_input, 4, '0'), '/',
        LPAD(month_input, 2, '0'), '/',
        LPAD(v_counter, 4, '0')
    ) AS transaction_number;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_transaction_number');

        // Drop table
        Schema::dropIfExists('counters');
    }
};
