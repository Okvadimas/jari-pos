<?php

namespace App\Repositories\Report;

use Illuminate\Support\Facades\DB;
use App\Models\RecommendationStock;

class RecommendationRepository
{
    /**
     * Get history list for a company, ordered by most recent first.
     */
    public static function getHistoryList(int $companyId, int $limit = 30)
    {
        return RecommendationStock::where('company_id', $companyId)
            ->orderBy('analysis_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get details for datatable, with optional moving_status filter.
     */
    public static function datatable(int $historyId, ?string $movingStatus = null)
    {
        $query = DB::table('recommendation_stock_details as rsd')
            ->join('product_variants as pv', 'pv.id', '=', 'rsd.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->where('rsd.recommendation_stock_id', $historyId)
            ->select(
                'rsd.id',
                'p.name as product_name',
                'pv.name as variant_name',
                'pv.sku',
                'c.name as category_name',
                'rsd.total_qty_sold',
                'rsd.total_revenue',
                'rsd.avg_daily_sales',
                'rsd.norm_qty',
                'rsd.norm_revenue',
                'rsd.score',
                'rsd.moving_status',
                'rsd.current_stock',
                'rsd.lead_time',
                'rsd.purchase_price',
                'rsd.sell_price',
                'rsd.safety_stock',
                'rsd.moq'
            )
            ->orderBy('rsd.score', 'desc');

        if ($movingStatus) {
            $query->where('rsd.moving_status', $movingStatus);
        }

        return $query;
    }

    /**
     * Get summary counts for a specific history.
     */
    public static function getSummary(int $historyId)
    {
        return RecommendationStock::find($historyId);
    }
}
