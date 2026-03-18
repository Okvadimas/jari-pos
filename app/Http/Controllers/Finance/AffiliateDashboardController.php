<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Finance\AffiliateDashboardService;

class AffiliateDashboardController extends Controller
{
    private $pageTitle = 'Dashboard Affiliate';

    public function index()
    {
        $data = [
            'startDate' => Carbon::now()->startOfMonth()->format('d/m/Y'),
            'endDate' => Carbon::now()->endOfMonth()->format('d/m/Y'),
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/affiliate-dashboard/index.js',
        ];

        return view('finance.affiliate-dashboard.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return AffiliateDashboardService::datatable($startDate, $endDate);
    }

    public function summary(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $stats = AffiliateDashboardService::getDashboardStats($startDate, $endDate);

        return $this->successResponse('Success', [
            'total_affiliate' => number_format($stats->total_affiliate, 0, ',', '.'),
            'total_transaksi' => number_format($stats->total_transaksi, 0, ',', '.'),
            'total_komisi' => 'Rp ' . number_format($stats->total_komisi, 0, ',', '.'),
            'komisi_pending' => 'Rp ' . number_format($stats->komisi_pending, 0, ',', '.'),
            'komisi_paid' => 'Rp ' . number_format($stats->komisi_paid, 0, ',', '.'),
            'total_penjualan' => 'Rp ' . number_format($stats->total_penjualan, 0, ',', '.'),
        ]);
    }

    public function detail(Request $request, $code)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return AffiliateDashboardService::affiliateDetail($code, $startDate, $endDate);
    }
}
