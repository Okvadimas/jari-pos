<?php

namespace App\Services\Report;

use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Report\RecommendationRepository;

class RecommendationService
{
    /**
     * Prepare DataTable for a specific analysis history.
     */
    public static function datatable(int $historyId, ?string $movingStatus = null)
    {
        $data = RecommendationRepository::datatable($historyId, $movingStatus);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('product_display', function ($row) {
                $variantHtml = '';
                if ($row->variant_name && $row->variant_name !== '-') {
                    $variantHtml = '<span class="d-block fs-12px text-soft mb-1">' . $row->variant_name . '</span>';
                }
                
                return '
                    <div class="user-card">
                        <div class="user-info">
                            <span class="tb-lead">' . $row->product_name . '</span>
                            ' . $variantHtml . '
                            <span class="d-block"><span class="badge bg-outline-secondary fs-11px">' . $row->category_name . '</span></span>
                        </div>
                    </div>';
            })
            ->addColumn('performance_display', function ($row) {
                $badges = [
                    'fast'   => '<span class="badge bg-success ms-1">Fast</span>',
                    'medium' => '<span class="badge bg-warning text-white ms-1">Medium</span>',
                    'slow'   => '<span class="badge" style="background-color:#fd7e14;color:#fff;" class="ms-1">Slow</span>',
                    'dead'   => '<span class="badge bg-danger ms-1">Dead</span>',
                ];
                $badge = $badges[$row->moving_status] ?? '';

                return '
                    <div class="d-flex flex-column" style="gap: 2px;">
                        <span class="fs-13px">Terjual: <strong>' . $row->total_qty_sold . '</strong> pcs' . $badge . '</span>
                        <span class="text-soft fs-12px">Rata: ' . number_format($row->avg_daily_sales, 1, ',', '.') . '/hari</span>
                    </div>';
            })
            ->addColumn('purchase_price_display', function ($row) {
                return 'Rp ' . number_format($row->purchase_price, 0, ',', '.');
            })
            ->addColumn('qty_recommendation', function ($row) {
                return '<div class="form-control-wrap number-spinner-wrap">
                            <input type="number" class="form-control form-control-sm form-control-number text-center qty-input" value="0" min="0" data-price="' . $row->purchase_price . '" data-id="' . $row->id . '" style="max-width: 85px; height: 50px; font-size: 14px; margin: 0 auto;">
                        </div>';
            })
            ->addColumn('estimated_nominal', function ($row) {
                return '<span class="fw-bold text-success estimated-nominal" id="est-' . $row->id . '" data-value="0">Rp 0</span>';
            })
            ->addColumn('ai_description', function ($row) {
                return '<span class="text-soft fst-italic fs-12px">Menunggu AI...</span>';
            })
            ->rawColumns(['product_display', 'performance_display', 'qty_recommendation', 'estimated_nominal', 'ai_description'])
            ->make(true);
    }
}
