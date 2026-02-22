<?php

namespace App\Services\Stock;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Repositories\Stock\StockDailyBalanceRepository;

class StockDailyBalanceService
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
                Log::error("StockDailyBalanceService::generate - Company {$company->id}: " . $e->getMessage());
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
     * Optimized: uses bulk SELECT queries + bulk writes instead of N+1 per variant.
     */
    private static function generateForCompany(Company $company): array
    {
        $today     = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $now       = Carbon::now()->format('Y-m-d H:i:s');

        return DB::transaction(function () use ($company, $today, $yesterday, $now) {

            // 1. Get all active variant IDs + current_stock
            $variants = StockDailyBalanceRepository::getActiveVariantsWithStock($company->id);

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
            $variantMap = $variants->keyBy('id');

            // 2. Yesterday's balances
            $yesterdayBalances = StockDailyBalanceRepository::getBalancesByDate($variantIds, $yesterday->format('Y-m-d'));

            // 3. Total sales qty per variant for yesterday
            $salesMap = StockDailyBalanceRepository::getDailySalesMap($variantIds, $yesterday->format('Y-m-d'));

            // 4. Total purchase qty per variant for yesterday
            $purchasesMap = StockDailyBalanceRepository::getDailyPurchasesMap($variantIds, $yesterday->format('Y-m-d'));

            // 5. Loop variants — compute closing, collect bulk writes
            $yesterdayUpserts = [];
            $todayUpserts     = [];
            $driftCorrections = [];
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

            // 6. BULK WRITE: upsert yesterday's daily balances
            $upsertColumns = [
                'opening_stock', 'in_stock', 'out_stock', 'adjustment_stock',
                'closing_stock', 'is_locked', 'updated_at',
            ];

            StockDailyBalanceRepository::upsertBalances($yesterdayUpserts, $upsertColumns);

            // 7. BULK WRITE: upsert today's daily balances
            StockDailyBalanceRepository::upsertBalances($todayUpserts, $upsertColumns);

            // 8. BULK WRITE: correct drifted product_variants
            if (!empty($driftCorrections)) {
                StockDailyBalanceRepository::bulkSetCurrentStock($driftCorrections, $now);
            }

            return [
                'company_id'   => $company->id,
                'company_name' => $company->name,
                'processed'    => $variants->count(),
                'corrected'    => $corrected,
            ];
        });
    }
}
