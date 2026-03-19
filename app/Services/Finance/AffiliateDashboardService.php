<?php

namespace App\Services\Finance;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Finance\AffiliateDashboardRepository;

class AffiliateDashboardService
{
    public static function datatable($startDate, $endDate)
    {
        $data = AffiliateDashboardRepository::affiliateSummary($startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('total_komisi', function ($row) {
                return 'Rp ' . number_format($row->total_komisi, 0, ',', '.');
            })
            ->editColumn('komisi_pending', function ($row) {
                return 'Rp ' . number_format($row->komisi_pending, 0, ',', '.');
            })
            ->editColumn('komisi_paid', function ($row) {
                return 'Rp ' . number_format($row->komisi_paid, 0, ',', '.');
            })
            ->editColumn('total_penjualan', function ($row) {
                return 'Rp ' . number_format($row->total_penjualan, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-dim btn-sm btn-outline-info" onclick="detailAffiliate(\'' . $row->affiliate_coupon_code . '\', \'' . addslashes($row->affiliate_name) . '\')"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function getDashboardStats($startDate, $endDate)
    {
        return AffiliateDashboardRepository::dashboardStats($startDate, $endDate);
    }

    public static function affiliateDetail($affiliateCode, $startDate, $endDate)
    {
        $data = AffiliateDashboardRepository::affiliateDetail($affiliateCode, $startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('sale_date', function ($row) {
                return $row->sale_date ? Carbon::parse($row->sale_date)->format('d M Y') : '-';
            })
            ->editColumn('sale_amount', function ($row) {
                return 'Rp ' . number_format($row->sale_amount, 0, ',', '.');
            })
            ->editColumn('commission_amount', function ($row) {
                return 'Rp ' . number_format($row->commission_amount, 0, ',', '.');
            })
            ->editColumn('commission_rate', function ($row) {
                return $row->commission_rate . '%';
            })
            ->editColumn('status', function ($row) {
                $labels = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'paid' => '<span class="badge bg-success">Dibayar</span>',
                    'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>',
                ];
                return $labels[$row->status] ?? '<span class="badge bg-secondary">' . $row->status . '</span>';
            })
            ->addColumn('type_badge', function ($row) {
                return $row->is_renewal ? '<span class="badge bg-info">Perpanjangan</span>' : '<span class="badge bg-primary">Baru</span>';
            })
            ->rawColumns(['status', 'type_badge'])
            ->make(true);
    }
}
