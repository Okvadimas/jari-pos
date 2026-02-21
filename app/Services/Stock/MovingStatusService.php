<?php

namespace App\Services\Stock;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Transaction\SalesRepository;
use App\Repositories\Inventory\ProductVariantRepository;
use App\Repositories\Report\RecommendationRepository;
use App\Models\ProductVariant;

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
     * Main entry point — called by both Artisan Command and Controller.
     *
     * @param int|null $companyId  If null, processes all companies.
     * @param int      $periodDays Number of days to analyze (default: 30).
     * @return array   Array of RecommendationStock records created.
     */
    public static function calculate(?int $companyId = null, int $periodDays = 30): array
    {
        $results = [];

        if ($companyId) {
            $companyIds = collect([$companyId]);
        } else {
            // Get all distinct company IDs that have products
            $companyIds = DB::table('products')
                ->whereNull('deleted_at')
                ->distinct()
                ->pluck('company_id')
                ->filter(); // Remove nulls
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
        $periodStart = $periodEnd->copy()->subDays($periodDays - 1);

        return DB::transaction(function () use ($companyId, $periodDays, $today, $periodStart, $periodEnd) {

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
            $variantMeta = DB::table('product_variants as pv')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->leftJoin('product_prices as pp', function ($join) {
                    $join->on('pp.product_variant_id', '=', 'pv.id')
                        ->where('pp.is_active', 1)
                        ->whereNull('pp.deleted_at');
                })
                ->where('p.company_id', $companyId)
                ->whereNull('pv.deleted_at')
                ->select('pv.id', 'pv.lead_time', 'pv.moq', 'pp.purchase_price', 'pp.sell_price')
                ->get()
                ->keyBy('id');

            // 5. Build analysis data for each variant
            $analysisData = collect();

            foreach ($variants as $variant) {
                $sales = $salesData->get($variant->id);
                $stock = $stockData->get($variant->id);
                $meta  = $variantMeta->get($variant->id);

                $totalQtySold  = $sales ? (int) $sales->total_qty_sold : 0;
                $totalRevenue  = $sales ? (float) $sales->total_revenue : 0;
                $avgDailySales = $totalQtySold / $periodDays;
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

            // 5. Normalize: Min-Max scaling
            $normQty     = self::normalize($analysisData->pluck('avg_daily_sales'));
            $normRevenue = self::normalize($analysisData->pluck('total_revenue'));

            // 9. Calculate score & classify
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

            // 10. Save history header (with cogs & gross profit balance)
            $history = RecommendationRepository::saveHistory([
                'company_id'           => $companyId,
                'analysis_date'        => $today->format('Y-m-d'),
                'period_days'          => $periodDays,
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

            // 8. Save details (with history_id)
            foreach ($details as &$detail) {
                $detail['recommendation_stock_id'] = $history->id;
            }
            unset($detail);

            RecommendationRepository::saveDetails($history->id, $details);

            // 9. Update product_variants.moving_status & moving_score
            self::updateVariantStatuses($details);

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
     * Bulk update product_variants with their latest moving_status and moving_score.
     * Uses individual updates wrapped in a single transaction for reliability.
     */
    private static function updateVariantStatuses(array $details): void
    {
        // Build case statements for a single bulk UPDATE query
        $ids      = [];
        $statuses = [];
        $scores   = [];

        foreach ($details as $detail) {
            $id = $detail['product_variant_id'];
            $ids[] = $id;
            $statuses[$id] = $detail['moving_status'];
            $scores[$id]   = $detail['score'];
        }

        // Use chunked updates to avoid overly large queries
        $chunks = array_chunk($ids, 500);

        foreach ($chunks as $chunkIds) {
            $statusCase = '';
            $scoreCase  = '';

            foreach ($chunkIds as $id) {
                $status = $statuses[$id];
                $score  = $scores[$id];
                $statusCase .= "WHEN {$id} THEN '{$status}' ";
                $scoreCase  .= "WHEN {$id} THEN {$score} ";
            }

            $idList = implode(',', $chunkIds);

            DB::statement("
                UPDATE product_variants
                SET moving_status = CASE id {$statusCase} END,
                    moving_score  = CASE id {$scoreCase} END
                WHERE id IN ({$idList})
            ");
        }
    }
}
