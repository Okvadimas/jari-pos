<?php

namespace App\Services\Analytics;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Transaction\SalesRepository;
use App\Repositories\Inventory\ProductVariantRepository;
use App\Repositories\Analytics\MovingStatusRepository;

class MovingStatusService
{
    /**
     * Weight constants for hybrid scoring.
     * Score = (WEIGHT_QTY × normalized_avg_daily_sales) + (WEIGHT_REVENUE × normalized_revenue)
     */
    const WEIGHT_QTY     = 0.6;
    const WEIGHT_REVENUE = 0.4;

    /**
     * Threshold constants for classification.
     */
    const FAST_THRESHOLD   = 0.70;
    const MEDIUM_THRESHOLD = 0.40;
    const SLOW_THRESHOLD   = 0.15;

    /**
     * Default analysis period in days.
     */
    const DEFAULT_PERIOD = 30;

    /**
     * Minimum required days of sales data before auto-updating min_stock.
     * Auto-update starts on day 8 (> 7 days).
     */
    const MIN_DATA_DAYS = 7;

    /**
     * Main entry point — called by both Artisan Command and Controller.
     *
     * @param int|null $companyId  If null, processes all companies.
     * @param int      $periodDays Number of days to analyze (default: 30).
     * @return array   Array of RecommendationStock records created.
     */
    public static function calculate(?int $companyId = null, int $periodDays = self::DEFAULT_PERIOD): array
    {
        $results = [];

        if ($companyId) {
            $companyIds = collect([$companyId]);
        } else {
            $companyIds = MovingStatusRepository::getActiveCompanyIds();
        }

        foreach ($companyIds as $cId) {
            try {
                $result = self::calculateForCompany($cId, $periodDays);
                $results[] = $result;
            } catch (\Throwable $e) {
                Log::error("MovingStatusService::calculate - Company {$cId}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Calculate moving status for a single company.
     */
    private static function calculateForCompany(int $companyId, int $periodDays): array
    {
        $today      = Carbon::today();
        $periodEnd  = $today->copy()->subDay(); // Yesterday (last full day of sales)

        // Adaptive period: use actual days if company has less than $periodDays of data
        $firstSaleDate = SalesRepository::getFirstSaleDate($companyId);
        $actualDays = $firstSaleDate ? (int) $firstSaleDate->diffInDays($periodEnd) + 1 : 0;
        $effectivePeriod = $actualDays > 0 ? min($periodDays, $actualDays) : $periodDays;

        $periodStart = $periodEnd->copy()->subDays($effectivePeriod - 1);

        // Check if enough data for auto-updating min_stock (> 7 days)
        $canAutoUpdateMinStock = $actualDays > self::MIN_DATA_DAYS;

        // Fetch company default_min_stock
        $companyDefault = MovingStatusRepository::getCompanyDefaultMinStock($companyId);

        return DB::transaction(function () use ($companyId, $periodDays, $effectivePeriod, $today, $periodStart, $periodEnd, $canAutoUpdateMinStock, $companyDefault) {

            // 1. Get all active variants
            $variants = ProductVariantRepository::getAllActive($companyId);

            if ($variants->isEmpty()) {
                return ['company_id' => $companyId, 'total_variants' => 0, 'message' => 'No active variants'];
            }

            // 2. Get sales data per variant
            $salesData = SalesRepository::getSalesPerVariant(
                $companyId,
                $periodStart->format('Y-m-d'),
                $periodEnd->format('Y-m-d')
            );

            // 3. Get latest stock per variant
            $stockData = ProductVariantRepository::getLatestStock($companyId);

            // 4. Get lead_time, moq, purchase_price, sell_price per variant
            $variantMeta = MovingStatusRepository::getVariantMeta($companyId);

            // 5. Build analysis data for each variant
            $analysisData = collect();

            foreach ($variants as $variant) {
                $sales = $salesData->get($variant->id);
                $stock = $stockData->get($variant->id);
                $meta  = $variantMeta->get($variant->id);

                $totalQtySold  = $sales ? (int) $sales->total_qty_sold : 0;
                $totalRevenue  = $sales ? (float) $sales->total_revenue : 0;
                $avgDailySales = $totalQtySold / $effectivePeriod;
                $currentStock  = $stock ? (int) $stock->closing_stock : 0;
                $leadTime      = $meta ? (int) $meta->lead_time : 1;
                $moq           = $meta ? (int) $meta->moq : 1;
                $hpp           = $meta ? (float) $meta->purchase_price : 0;
                $sellPrice     = $meta ? (float) $meta->sell_price : 0;
                $safetyStock   = (int) ceil($avgDailySales * $leadTime);
                $totalCogs     = $sales ? (float) $sales->total_cogs : 0;

                $analysisData->push([
                    'variant_id'      => $variant->id,
                    'total_qty_sold'  => $totalQtySold,
                    'total_revenue'   => $totalRevenue,
                    'avg_daily_sales' => $avgDailySales,
                    'current_stock'   => $currentStock,
                    'lead_time'       => $leadTime,
                    'moq'             => $moq,
                    'purchase_price'  => $hpp,
                    'sell_price'      => $sellPrice,
                    'safety_stock'    => $safetyStock,
                    'total_cogs'      => $totalCogs,
                ]);
            }

            // 6. Normalize: Min-Max scaling
            $normQty     = self::normalize($analysisData->pluck('avg_daily_sales'));
            $normRevenue = self::normalize($analysisData->pluck('total_revenue'));

            // 7. Calculate score & classify
            $counters = ['fast' => 0, 'medium' => 0, 'slow' => 0, 'dead' => 0];
            $details  = [];
            $now      = Carbon::now()->format('Y-m-d H:i:s');
            $totalCogs  = 0;
            $totalRev   = 0;

            foreach ($analysisData as $i => $item) {
                $nq = $normQty[$i];
                $nr = $normRevenue[$i];
                $score = round((self::WEIGHT_QTY * $nq) + (self::WEIGHT_REVENUE * $nr), 4);
                $status = self::classify($score);
                $counters[$status]++;

                $totalCogs += $item['total_cogs'];
                $totalRev  += $item['total_revenue'];

                $details[] = [
                    'product_variant_id' => $item['variant_id'],
                    'total_qty_sold'     => $item['total_qty_sold'],
                    'total_revenue'      => $item['total_revenue'],
                    'avg_daily_sales'    => round($item['avg_daily_sales'], 4),
                    'norm_qty'           => round($nq, 4),
                    'norm_revenue'       => round($nr, 4),
                    'score'              => $score,
                    'moving_status'      => $status,
                    'current_stock'      => $item['current_stock'],
                    'lead_time'          => $item['lead_time'],
                    'purchase_price'     => $item['purchase_price'],
                    'sell_price'         => $item['sell_price'],
                    'safety_stock'       => $item['safety_stock'],
                    'moq'                => $item['moq'],
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }

            // 8. Save history header (with cogs & gross profit balance)
            $history = MovingStatusRepository::saveHistory([
                'company_id'           => $companyId,
                'analysis_date'        => $today->format('Y-m-d'),
                'period_days'          => $effectivePeriod,
                'period_start'         => $periodStart->format('Y-m-d'),
                'period_end'           => $periodEnd->format('Y-m-d'),
                'total_variants'       => count($details),
                'total_fast'           => $counters['fast'],
                'total_medium'         => $counters['medium'],
                'total_slow'           => $counters['slow'],
                'total_dead'           => $counters['dead'],
                'cogs_balance'         => $totalCogs,
                'gross_profit_balance' => $totalRev - $totalCogs,
            ]);

            // 9. Save details (with history_id)
            foreach ($details as &$detail) {
                $detail['recommendation_stock_id'] = $history->id;
            }
            unset($detail);

            MovingStatusRepository::saveDetails($history->id, $details);

            // 10. Update product_variants.moving_status, moving_score & min_stock
            self::updateVariantStatuses($details, $canAutoUpdateMinStock, $companyDefault);

            return [
                'company_id'     => $companyId,
                'history_id'     => $history->id,
                'total_variants' => count($details),
                'fast'           => $counters['fast'],
                'medium'         => $counters['medium'],
                'slow'           => $counters['slow'],
                'dead'           => $counters['dead'],
            ];
        });
    }

    /**
     * Min-Max normalization for a collection of values.
     * Returns array of normalized values (0.0 – 1.0).
     */
    private static function normalize(Collection $values): array
    {
        $min = $values->min();
        $max = $values->max();
        $range = $max - $min;

        if ($range == 0) {
            // All values are the same — normalize to 0
            return $values->map(fn() => 0.0)->toArray();
        }

        return $values->map(fn($v) => ($v - $min) / $range)->toArray();
    }

    /**
     * Classify a hybrid score into a moving status.
     */
    private static function classify(float $score): string
    {
        if ($score >= self::FAST_THRESHOLD) return 'fast';
        if ($score >= self::MEDIUM_THRESHOLD) return 'medium';
        if ($score >= self::SLOW_THRESHOLD) return 'slow';
        return 'dead';
    }

    /**
     * Build data for bulk updating product_variants and delegate to repository.
     */
    private static function updateVariantStatuses(array $details, bool $canAutoUpdateMinStock, int $companyDefault): void
    {
        $ids      = [];
        $statuses = [];
        $scores   = [];
        $minStocks = [];

        // Fetch min_stock_custom flags for all variants (bulk)
        $allIds = array_column($details, 'product_variant_id');
        $customFlags = MovingStatusRepository::getCustomFlags($allIds);

        foreach ($details as $detail) {
            $id = $detail['product_variant_id'];
            $ids[] = $id;
            $statuses[$id] = $detail['moving_status'];
            $scores[$id]   = $detail['score'];

            // Auto-update min_stock if:
            // 1. canAutoUpdateMinStock (data > 7 days)
            // 2. min_stock_custom = false
            // 3. safety_stock > 0 (product has been sold)
            $isCustom = (bool) ($customFlags[$id] ?? false);
            if ($canAutoUpdateMinStock && !$isCustom && $detail['safety_stock'] > 0) {
                $minStocks[$id] = max($detail['safety_stock'], $companyDefault);
            }
        }

        MovingStatusRepository::bulkUpdateStatuses($ids, $statuses, $scores, $minStocks);
    }
}
