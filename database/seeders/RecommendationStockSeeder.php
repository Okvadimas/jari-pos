<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecommendationStockSeeder extends Seeder
{
    /**
     * Seed recommendation_stocks with one analysis record for today.
     *
     * Run: php artisan db:seed --class=RecommendationStockSeeder
     */
    public function run(): void
    {
        $this->command->info('📊 Generating recommendation_stocks...');

        $companyId  = 1;
        $today      = Carbon::today();
        $periodEnd  = $today->copy()->subDay();
        $periodStart = $periodEnd->copy()->subDays(29);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // Counts will be updated by RecommendationStockDetailSeeder
        DB::table('recommendation_stocks')->updateOrInsert(
            [
                'company_id'    => $companyId,
                'analysis_date' => $today->format('Y-m-d'),
            ],
            [
                'period_days'    => 30,
                'period_start'   => $periodStart->format('Y-m-d'),
                'period_end'     => $periodEnd->format('Y-m-d'),
                'total_variants' => 0,
                'total_fast'     => 0,
                'total_medium'   => 0,
                'total_slow'     => 0,
                'total_dead'     => 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]
        );

        $this->command->info('  ✅ recommendation_stocks record created.');
    }
}
