<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecommendationStockDetailSeeder extends Seeder
{
    /**
     * Seed recommendation_stock_details: calculate scores from sales data, insert details,
     * and update the parent recommendation_stocks with counts.
     *
     * Depends on: RecommendationStockSeeder, StockDailyBalanceSeeder, SalesSeeder
     * Run: php artisan db:seed --class=RecommendationStockDetailSeeder
     */
    public function run(): void
    {
        $this->command->info('📋 Generating recommendation_stock_details...');

        $companyId  = 1;
        $today      = Carbon::today();
        $periodEnd  = $today->copy()->subDay();
        $periodStart = $periodEnd->copy()->subDays(29);
        $periodDays = 30;
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // Get history record
        $history = DB::table('recommendation_stocks')
            ->where('company_id', $companyId)
            ->where('analysis_date', $today->format('Y-m-d'))
            ->first();

        if (!$history) {
            $this->command->error('❌ No recommendation_stocks found. Run RecommendationStockSeeder first.');
            return;
        }

        // Get all active variants
        $variants = DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('p.company_id', $companyId)
            ->whereNull('pv.deleted_at')
            ->whereNull('p.deleted_at')
            ->pluck('pv.id');

        // Get sales data
        $salesData = DB::table('sales_order_details as sod')
            ->join('sales_orders as so', 'so.id', '=', 'sod.sales_order_id')
            ->where('so.company_id', $companyId)
            ->whereBetween('so.order_date', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
            ->whereNull('so.deleted_at')
            ->whereNull('sod.deleted_at')
            ->groupBy('sod.product_variant_id')
            ->select(
                'sod.product_variant_id',
                DB::raw('SUM(sod.quantity) as total_qty_sold'),
                DB::raw('SUM(sod.subtotal) as total_revenue')
            )
            ->get()
            ->keyBy('product_variant_id');

        // Get latest stock
        $latestStocks = DB::table('stock_daily_balances')
            ->where('date', $periodEnd->format('Y-m-d'))
            ->whereNull('deleted_at')
            ->pluck('closing_stock', 'product_variant_id');

        // Synthetic profiles for variants without real sales
        $syntheticProfiles = [
            1 => 11, 5 => 15, 6 => 10, 18 => 8, 19 => 12, 20 => 14, 26 => 18, 23 => 9,
            2 => 5,  7 => 6,  10 => 4, 13 => 5, 14 => 4,  21 => 6,  24 => 5,  25 => 4,  30 => 3, 31 => 4,
            3 => 2,  8 => 2,  11 => 1, 15 => 1, 16 => 2,  22 => 1,  27 => 1,  28 => 2,  29 => 1, 32 => 1,
            4 => 0,  9 => 0,  12 => 0, 17 => 0, 33 => 0,  34 => 0,  35 => 0,  36 => 0,  37 => 0, 38 => 0, 39 => 0, 40 => 0,
        ];

        // Build analysis
        $analysisData = [];
        foreach ($variants as $variantId) {
            $sales = $salesData->get($variantId);
            $totalQty = $sales ? (int) $sales->total_qty_sold : 0;
            $totalRev = $sales ? (float) $sales->total_revenue : 0;

            // Synthetic fallback
            if ($totalQty === 0 && isset($syntheticProfiles[$variantId])) {
                $avgPerDay = $syntheticProfiles[$variantId];
                $totalQty  = (int) round($avgPerDay * $periodDays * (0.8 + (rand(0, 40) / 100)));
                $totalRev  = $totalQty * rand(5, 200) * 1000;
            }

            $analysisData[$variantId] = [
                'total_qty_sold'  => $totalQty,
                'total_revenue'   => $totalRev,
                'avg_daily_sales' => $totalQty / $periodDays,
                'current_stock'   => $latestStocks->get($variantId, 0),
            ];
        }

        // Normalize (Min-Max)
        $avgValues = collect($analysisData)->pluck('avg_daily_sales');
        $revValues = collect($analysisData)->pluck('total_revenue');

        $minAvg = $avgValues->min(); $maxAvg = $avgValues->max(); $rangeAvg = $maxAvg - $minAvg;
        $minRev = $revValues->min(); $maxRev = $revValues->max(); $rangeRev = $maxRev - $minRev;

        $counters = ['fast' => 0, 'medium' => 0, 'slow' => 0, 'dead' => 0];
        $records = [];

        foreach ($analysisData as $variantId => $data) {
            $normQty = $rangeAvg > 0 ? ($data['avg_daily_sales'] - $minAvg) / $rangeAvg : 0;
            $normRev = $rangeRev > 0 ? ($data['total_revenue'] - $minRev) / $rangeRev : 0;

            $score = round((0.6 * $normQty) + (0.4 * $normRev), 4);

            if ($score >= 0.70) $status = 'fast';
            elseif ($score >= 0.40) $status = 'medium';
            elseif ($score >= 0.15) $status = 'slow';
            else $status = 'dead';

            $counters[$status]++;

            $records[] = [
                'recommendation_stock_id'  => $history->id,
                'product_variant_id'       => $variantId,
                'total_qty_sold'           => $data['total_qty_sold'],
                'total_revenue'            => $data['total_revenue'],
                'avg_daily_sales'          => round($data['avg_daily_sales'], 4),
                'norm_qty'                 => round($normQty, 4),
                'norm_revenue'             => round($normRev, 4),
                'score'                    => $score,
                'moving_status'            => $status,
                'current_stock'            => $data['current_stock'],
                'created_at'               => $now,
                'updated_at'               => $now,
            ];

            // Update product_variants
            DB::table('product_variants')
                ->where('id', $variantId)
                ->update([
                    'moving_status' => $status,
                    'moving_score'  => $score,
                    'updated_at'    => $now,
                ]);
        }

        // Insert details
        DB::table('recommendation_stock_details')->insert($records);

        // Update history counts
        DB::table('recommendation_stocks')
            ->where('id', $history->id)
            ->update([
                'total_variants' => count($records),
                'total_fast'     => $counters['fast'],
                'total_medium'   => $counters['medium'],
                'total_slow'     => $counters['slow'],
                'total_dead'     => $counters['dead'],
                'updated_at'     => $now,
            ]);

        $this->command->info('  ✅ ' . count($records) . ' recommendation_stock_details created.');
        $this->command->table(
            ['Status', 'Count'],
            [
                ['🟢 Fast', $counters['fast']],
                ['🟡 Medium', $counters['medium']],
                ['🟠 Slow', $counters['slow']],
                ['🔴 Dead', $counters['dead']],
            ]
        );
    }
}
