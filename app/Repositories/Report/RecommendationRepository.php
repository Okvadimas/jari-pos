<?php

namespace App\Repositories\Report;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\ProductVariant;
use App\Models\RecommendationStock;
use App\Models\RecommendationStockDetail;

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
    public static function datatable($historyId = null)
    {
        if ($historyId) {
            // Get the details connected to standard product/variant info
            $query = RecommendationStockDetail::from('recommendation_stock_details as rsd')
                ->join('recommendation_stocks as rs', 'rs.id', '=', 'rsd.recommendation_stock_id')
                ->join('product_variants as pv', 'pv.id', '=', 'rsd.product_variant_id')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->where('rsd.recommendation_stock_id', $historyId)
                ->select([
                    'rsd.*',
                    'p.name as product_name',
                    'pv.name as variant_name',
                    'c.name as category_name',
                    'pv.current_stock as live_stock'
                ])
                ->orderByRaw("FIELD(rsd.moving_status, 'fast', 'medium', 'slow', 'dead')");
        } else {
            $query = RecommendationStock::query()
                ->orderBy('id', 'desc');
        }

        return $query;
    }

    /**
     * Get summary counts for a specific history.
     */
    public static function getSummary()
    {
        return DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('pv.moving_status', '!=', null)
            ->where('p.company_id', Session::get('company_id'))
            ->groupBy('pv.moving_status')
            ->select('pv.moving_status', DB::raw('count(*) as total'))
            ->get();
    }
}
