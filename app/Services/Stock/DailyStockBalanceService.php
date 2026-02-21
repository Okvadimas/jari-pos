<?php

namespace App\Services\Stock;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Company;

class DailyStockBalanceService
{
    /**
     * Main entry point — called by both Artisan Command and Controller.
     *
     * @param int|null $companyId  If null, processes all active companies.
     * @return array   Array of per-company result summaries.
     */
    public static function generate(?int $companyId = null): array
    {
        $results = [];

        $companies = $companyId
            ? Company::where('id', $companyId)->get()
            : Company::whereNull('deleted_at')->get();

        foreach ($companies as $company) {
            try {
                $result = self::generateForCompany($company);
                $results[] = $result;
            } catch (\Throwable $e) {
                Log::error("DailyStockBalanceService::generate - Company {$company->id}: " . $e->getMessage());
                $results[] = [
                    'company_id'   => $company->id,
                    'company_name' => $company->name,
                    'processed'    => 0,
                    'corrected'    => 0,
                    'error'        => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Generate stock daily balances for a single company.
     *
     * Optimized: uses 3 bulk SELECT queries + bulk writes instead of N+1 per variant.
     */
    private static function generateForCompany(Company $company): array
    {
        $today     = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $now       = Carbon::now()->format('Y-m-d H:i:s');

        return DB::transaction(function () use ($company, $today, $yesterday, $now) {

            // ──────────────────────────────────────────────
            // 1. Get all active variant IDs + current_stock
            // ──────────────────────────────────────────────
            $variants = DB::table('product_variants as pv')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->where('p.company_id', $company->id)
                ->whereNull('pv.deleted_at')
                ->whereNull('p.deleted_at')
                ->select('pv.id', 'pv.current_stock')
                ->get();

            if ($variants->isEmpty()) {
                return [
                    'company_id'   => $company->id,
                    'company_name' => $company->name,
                    'processed'    => 0,
                    'corrected'    => 0,
                    'message'      => 'No active variants',
                ];
            }

            $variantIds = $variants->pluck('id')->toArray();
            $variantMap = $variants->keyBy('id'); // id => {id, current_stock}

            // ──────────────────────────────────────────────────────────────
            // 2. BULK: yesterday's balances, keyed by product_variant_id
            // ──────────────────────────────────────────────────────────────
            $yesterdayBalances = DB::table('stock_daily_balances')
                ->whereIn('product_variant_id', $variantIds)
                ->where('date', $yesterday->format('Y-m-d'))
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('product_variant_id');

            // ──────────────────────────────────────────────────────────────
            // 3. BULK: total sales qty per variant for yesterday
            // ──────────────────────────────────────────────────────────────
            $salesMap = DB::table('sales_order_details as sod')
                ->join('sales_orders as so', 'so.id', '=', 'sod.sales_order_id')
                ->whereIn('sod.product_variant_id', $variantIds)
                ->whereDate('so.order_date', $yesterday)
                ->whereNull('so.deleted_at')
                ->whereNull('sod.deleted_at')
                ->groupBy('sod.product_variant_id')
                ->selectRaw('sod.product_variant_id, COALESCE(SUM(sod.quantity), 0) as total_qty')
                ->pluck('total_qty', 'product_variant_id');

            // ──────────────────────────────────────────────────────────────
            // 4. BULK: total purchase qty per variant for yesterday
            // ──────────────────────────────────────────────────────────────
            $purchasesMap = DB::table('purchase_details as pd')
                ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
                ->whereIn('pd.product_variant_id', $variantIds)
                ->whereDate('p.purchase_date', $yesterday)
                ->whereNull('p.deleted_at')
                ->whereNull('pd.deleted_at')
                ->groupBy('pd.product_variant_id')
                ->selectRaw('pd.product_variant_id, COALESCE(SUM(pd.quantity), 0) as total_qty')
                ->pluck('total_qty', 'product_variant_id');

            // ──────────────────────────────────────────────────────────
            // 5. Loop variants — compute closing, collect bulk writes
            // ──────────────────────────────────────────────────────────
            $yesterdayUpserts = [];
            $todayUpserts     = [];
            $driftCorrections = []; // variant_id => trueClosing
            $corrected = 0;

            foreach ($variants as $variant) {
                $yesterdayBalance = $yesterdayBalances->get($variant->id);

                $openingYesterday = $yesterdayBalance
                    ? $yesterdayBalance->opening_stock
                    : $variant->current_stock;

                $salesQty    = (int) ($salesMap[$variant->id] ?? 0);
                $purchaseQty = (int) ($purchasesMap[$variant->id] ?? 0);
                $trueClosing = $openingYesterday + $purchaseQty - $salesQty;

                // Yesterday's balance row (upsert)
                $yesterdayUpserts[] = [
                    'product_variant_id' => $variant->id,
                    'date'               => $yesterday->format('Y-m-d'),
                    'opening_stock'      => $openingYesterday,
                    'in_stock'           => $purchaseQty,
                    'out_stock'          => $salesQty,
                    'adjustment_stock'   => 0,
                    'closing_stock'      => $trueClosing,
                    'is_locked'          => 1,
                    'created_by'         => 0,
                    'updated_by'         => 0,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                // Today's opening row (upsert)
                $todayUpserts[] = [
                    'product_variant_id' => $variant->id,
                    'date'               => $today->format('Y-m-d'),
                    'opening_stock'      => $trueClosing,
                    'in_stock'           => 0,
                    'out_stock'          => 0,
                    'adjustment_stock'   => 0,
                    'closing_stock'      => $trueClosing,
                    'is_locked'          => 0,
                    'created_by'         => 0,
                    'updated_by'         => 0,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                // Drift detection
                if ((int) $variant->current_stock !== $trueClosing) {
                    $drift = $variant->current_stock - $trueClosing;
                    Log::warning("Stock drift detected: variant_id={$variant->id}, current={$variant->current_stock}, true={$trueClosing}, drift={$drift}");
                    $driftCorrections[$variant->id] = $trueClosing;
                    $corrected++;
                }
            }

            // ──────────────────────────────────────────────────
            // 6. BULK WRITE: upsert yesterday's daily balances
            // ──────────────────────────────────────────────────
            $upsertColumns = [
                'opening_stock', 'in_stock', 'out_stock', 'adjustment_stock',
                'closing_stock', 'is_locked', 'updated_at',
            ];

            foreach (array_chunk($yesterdayUpserts, 500) as $chunk) {
                DB::table('stock_daily_balances')->upsert(
                    $chunk,
                    ['product_variant_id', 'date'], // unique key
                    $upsertColumns
                );
            }

            // ──────────────────────────────────────────────────
            // 7. BULK WRITE: upsert today's daily balances
            // ──────────────────────────────────────────────────
            foreach (array_chunk($todayUpserts, 500) as $chunk) {
                DB::table('stock_daily_balances')->upsert(
                    $chunk,
                    ['product_variant_id', 'date'],
                    $upsertColumns
                );
            }

            // ──────────────────────────────────────────────────
            // 8. BULK WRITE: correct drifted product_variants
            // ──────────────────────────────────────────────────
            if (!empty($driftCorrections)) {
                self::bulkUpdateCurrentStock($driftCorrections, $now);
            }

            return [
                'company_id'   => $company->id,
                'company_name' => $company->name,
                'processed'    => $variants->count(),
                'corrected'    => $corrected,
            ];
        });
    }

    /**
     * Bulk update product_variants.current_stock using a single CASE WHEN query.
     *
     * @param array  $corrections  [variant_id => trueClosing, ...]
     * @param string $now          Timestamp for updated_at
     */
    private static function bulkUpdateCurrentStock(array $corrections, string $now): void
    {
        foreach (array_chunk($corrections, 500, true) as $chunk) {
            $caseWhen = '';
            $ids = [];

            foreach ($chunk as $variantId => $trueClosing) {
                $ids[] = $variantId;
                $caseWhen .= "WHEN {$variantId} THEN {$trueClosing} ";
            }

            $idList = implode(',', $ids);

            DB::statement("
                UPDATE product_variants
                SET current_stock = CASE id {$caseWhen} END,
                    updated_at = '{$now}'
                WHERE id IN ({$idList})
            ");
        }
    }
}
