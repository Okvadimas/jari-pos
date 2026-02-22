<?php

namespace App\Repositories\Analytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RecommendationStock;
use App\Models\RecommendationStockDetail;

class MovingStatusRepository
{
    /**
     * Save or update the analysis history header.
     * Uses updateOrCreate to allow re-running on the same day.
     */
    public static function saveHistory(array $data): RecommendationStock
    {
        return RecommendationStock::updateOrCreate(
            [
                'company_id'    => $data['company_id'],
                'analysis_date' => $data['analysis_date'],
            ],
            $data
        );
    }

    /**
     * Bulk insert analysis details. Deletes old details for the same history first.
     */
    public static function saveDetails(int $historyId, array $details): void
    {
        // Delete existing details for this history (allows re-run)
        RecommendationStockDetail::where('recommendation_stock_id', $historyId)->delete();

        // Chunk insert for performance
        $chunks = array_chunk($details, 500);
        foreach ($chunks as $chunk) {
            RecommendationStockDetail::insert($chunk);
        }
    }

    /**
     * Get all distinct company IDs that have active products.
     */
    public static function getActiveCompanyIds(): Collection
    {
        return DB::table('products')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('company_id')
            ->filter();
    }

    /**
     * Get default_min_stock for a company.
     */
    public static function getCompanyDefaultMinStock(int $companyId): int
    {
        return (int) (DB::table('companies')->where('id', $companyId)->value('default_min_stock') ?? 10);
    }

    /**
     * Get lead_time, moq, purchase_price, sell_price per variant for a company.
     */
    public static function getVariantMeta(int $companyId): Collection
    {
        return DB::table('product_variants as pv')
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
    }

    /**
     * Get min_stock_custom flags for given variant IDs.
     */
    public static function getCustomFlags(array $ids): Collection
    {
        return DB::table('product_variants')
            ->whereIn('id', $ids)
            ->pluck('min_stock_custom', 'id');
    }

    /**
     * Bulk update moving_status, moving_score, and optionally min_stock.
     */
    public static function bulkUpdateStatuses(array $ids, array $statuses, array $scores, array $minStocks = []): void
    {
        $chunks = array_chunk($ids, 500);

        foreach ($chunks as $chunkIds) {
            $statusCase   = '';
            $scoreCase    = '';
            $minStockCase = '';
            $hasMinStock  = false;

            foreach ($chunkIds as $id) {
                $statusCase .= "WHEN {$id} THEN '{$statuses[$id]}' ";
                $scoreCase  .= "WHEN {$id} THEN {$scores[$id]} ";

                if (isset($minStocks[$id])) {
                    $minStockCase .= "WHEN {$id} THEN {$minStocks[$id]} ";
                    $hasMinStock = true;
                }
            }

            $idList = implode(',', $chunkIds);
            $minStockSql = $hasMinStock
                ? ", min_stock = CASE id {$minStockCase} ELSE min_stock END"
                : '';

            DB::statement("
                UPDATE product_variants
                SET moving_status = CASE id {$statusCase} END,
                    moving_score  = CASE id {$scoreCase} END
                    {$minStockSql}
                WHERE id IN ({$idList})
            ");
        }
    }
}
