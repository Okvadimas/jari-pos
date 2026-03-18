<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Finance\AffiliateCommissionService;
use App\Models\AffiliateCommission;

class AffiliateCommissionController extends Controller
{
    private $pageTitle = 'Komisi Affiliate';

    public function index()
    {
        $data = [
            'startDate' => Carbon::now()->startOfMonth()->format('d/m/Y'),
            'endDate' => Carbon::now()->endOfMonth()->format('d/m/Y'),
            'title' => $this->pageTitle,
            'css' => 'resources/css/pages/finance/affiliate-commission/index.css',
            'js' => 'resources/js/pages/finance/affiliate-commission/index.js',
        ];

        return view('finance.affiliate-commission.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return AffiliateCommissionService::datatable($startDate, $endDate);
    }

    public function summary(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $summary = AffiliateCommissionService::getSummary($startDate, $endDate);

        return $this->successResponse('Success', [
            'total_komisi' => number_format($summary->total_komisi, 0, ',', '.'),
            'total_nominal' => 'Rp ' . number_format($summary->total_nominal, 0, ',', '.'),
            'total_pending' => 'Rp ' . number_format($summary->total_pending, 0, ',', '.'),
            'total_paid' => 'Rp ' . number_format($summary->total_paid, 0, ',', '.'),
        ]);
    }

    public function show($id)
    {
        $commission = AffiliateCommission::with(['company', 'appSale'])->findOrFail($id);

        return $this->successResponse('Success', [
            'commission' => $commission,
            'sale' => $commission->appSale,
        ]);
    }

    public function markAsPaid($id)
    {
        $process = AffiliateCommissionService::markAsPaid($id);
        return $process ? $this->successResponse('Komisi berhasil ditandai sudah dibayar') : $this->errorResponse('Gagal mengupdate status komisi');
    }

    public function cancel($id)
    {
        $process = AffiliateCommissionService::cancel($id);
        return $process ? $this->successResponse('Komisi berhasil dibatalkan') : $this->errorResponse('Gagal membatalkan komisi');
    }
}
