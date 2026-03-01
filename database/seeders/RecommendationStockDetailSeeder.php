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
            ->select('pv.id', 'pv.lead_time', 'pv.moq', 'pv.min_stock')
            ->get()
            ->keyBy('id');

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
                DB::raw('SUM(sod.subtotal) as total_revenue'),
                DB::raw('SUM(sod.quantity * sod.purchase_price) as total_cogs'),
                DB::raw('AVG(sod.purchase_price) as avg_purchase_price'),
                DB::raw('AVG(sod.sell_price) as avg_sell_price')
            )
            ->get()
            ->keyBy('product_variant_id');

        // Get latest stock
        $latestStocks = DB::table('stock_daily_balances')
            ->where('date', $periodEnd->format('Y-m-d'))
            ->whereNull('deleted_at')
            ->pluck('closing_stock', 'product_variant_id');

        // Synthetic profiles for variants without real sales (avg qty sold per day)
        $syntheticProfiles = [
            // Pelet Ikan Koi
            1 => 1.5, 2 => 3, 3 => 4, 4 => 5,
            // Pelet Lele
            5 => 0.5, 6 => 2, 7 => 3,
            // Pelet Ikan Hias
            8 => 2, 9 => 3,
            // Voer Burung
            10 => 3, 11 => 2, 12 => 4,
            // Milet Putih
            13 => 2, 14 => 3, 15 => 1.5,
            // Kroto
            16 => 4, 17 => 6,
            // Cat Food Adult
            18 => 0.5, 19 => 1.5, 20 => 5, 21 => 4, 22 => 10,
            // Cat Food Kitten
            23 => 1, 24 => 3, 25 => 4,
            // Wet Food
            26 => 20,
            // Dog Food Adult
            27 => 0.2, 28 => 0.5, 29 => 1,
            // Dog Food Puppy
            30 => 0.5, 31 => 1.5, 32 => 3,
            // Accessories & Meds
            33 => 1, 34 => 0.5, 35 => 0.3, 36 => 0.1,
            37 => 2, 38 => 3, 39 => 2, 40 => 1,
        ];

        // Realistic base prices for each variant
        $basePrices = [
            1 => 50000, 2 => 25000, 3 => 13500, 4 => 6000,
            5 => 105000, 6 => 22000, 7 => 11500,
            8 => 15000, 9 => 8500,
            10 => 35000, 11 => 18000, 12 => 7500,
            13 => 18000, 14 => 9500, 15 => 5000,
            16 => 25000, 17 => 13000,
            18 => 285000, 19 => 92000, 20 => 33000, 21 => 17500, 22 => 4500,
            23 => 105000, 24 => 38000, 25 => 19500,
            26 => 6500,
            27 => 455000, 28 => 245000, 29 => 115000,
            30 => 325000, 31 => 110000, 32 => 39000,
            33 => 45000,
            34 => 85000, 35 => 125000, 36 => 175000,
            37 => 35000, 38 => 20000,
            39 => 55000, 40 => 150000,
        ];

        $totalCogsBalance = 0;
        $totalGrossProfitBalance = 0;
        $totalEstimatedNominal = 0;

        // Build analysis
        $analysisData = [];
        foreach ($variants as $variantId => $variant) {
            $sales = $salesData->get($variantId);
            $totalQty = $sales ? (int) $sales->total_qty_sold : 0;
            $totalRev = $sales ? (float) $sales->total_revenue : 0;
            $totalCogs = $sales ? (float) $sales->total_cogs : 0;
            $avgPurchasePrice = $sales ? (float) $sales->avg_purchase_price : 0;
            $avgSellPrice = $sales ? (float) $sales->avg_sell_price : 0;

            // Synthetic fallback
            if ($totalQty === 0 && isset($syntheticProfiles[$variantId])) {
                $avgPerDay = $syntheticProfiles[$variantId];
                // Introduce some realistic variance (+/- 20% qty)
                $multiplier = 0.8 + (rand(0, 40) / 100);
                $totalQty  = (int) ceil($avgPerDay * $periodDays * $multiplier);
                
                if ($totalQty > 0) {
                    $baseSellPrice = $basePrices[$variantId] ?? 10000;
                    $avgSellPrice = $baseSellPrice;
                    $totalRev = $totalQty * $avgSellPrice;
                    
                    // Purchase price is ~65-75% of sell price (25-35% margin)
                    $margin = 0.65 + (rand(0, 10) / 100);
                    $avgPurchasePrice = $avgSellPrice * $margin; 
                    $totalCogs = $totalQty * $avgPurchasePrice;
                }
            }

            $currentStock = $latestStocks->get($variantId, 0);

            $analysisData[$variantId] = [
                'total_qty_sold'     => $totalQty,
                'total_revenue'      => $totalRev,
                'total_cogs'         => $totalCogs,
                'avg_daily_sales'    => $totalQty / $periodDays,
                'current_stock'      => $currentStock,
                'avg_purchase_price' => $avgPurchasePrice,
                'avg_sell_price'     => $avgSellPrice,
                'lead_time'          => $variant->lead_time ?? 0,
                'moq'                => $variant->moq ?? 0,
                'safety_stock'       => $variant->min_stock ?? 0,
            ];

            $totalCogsBalance += $totalCogs;
            $totalGrossProfitBalance += ($totalRev - $totalCogs);
            $totalEstimatedNominal += ($currentStock * $avgPurchasePrice);
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

            // Calculate qty_restock
            $qtyRestock = 0;
            if ($data['current_stock'] < $data['safety_stock']) {
                $deficit = $data['safety_stock'] - $data['current_stock'];
                $qtyRestock = max($deficit, $data['moq']);
            }

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
                'lead_time'                => $data['lead_time'],
                'purchase_price'           => $data['avg_purchase_price'],
                'sell_price'               => $data['avg_sell_price'],
                'safety_stock'             => $data['safety_stock'],
                'qty_restock'              => $qtyRestock,
                'moq'                      => $data['moq'],
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
                'total_variants'          => count($records),
                'total_fast'              => $counters['fast'],
                'total_medium'            => $counters['medium'],
                'total_slow'              => $counters['slow'],
                'total_dead'              => $counters['dead'],
                'cogs_balance'            => $totalCogsBalance,
                'gross_profit_balance'    => $totalGrossProfitBalance,
                'total_estimated_nominal' => $totalEstimatedNominal,
                'updated_at'              => $now,
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
