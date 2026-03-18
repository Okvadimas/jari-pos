<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Finance\AppSaleService;
use App\Models\AppSale;
use App\Http\Requests\Finance\AppSale\StoreAppSaleRequest;

class AppSaleController extends Controller
{
    private $pageTitle = 'Penjualan Aplikasi';

    public function index()
    {
        $data = [
            'startDate' => Carbon::now()->startOfMonth()->format('d/m/Y'),
            'endDate' => Carbon::now()->endOfMonth()->format('d/m/Y'),
            'title' => $this->pageTitle,
            'css' => 'resources/css/pages/finance/app-sale/index.css',
            'js' => 'resources/js/pages/finance/app-sale/index.js',
        ];

        return view('finance.app-sale.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return AppSaleService::datatable($startDate, $endDate);
    }

    public function summary(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $summary = AppSaleService::getSummary($startDate, $endDate);

        return $this->successResponse('Success', [
            'total_transaksi' => number_format($summary->total_transaksi, 0, ',', '.'),
            'total_pemasukan' => 'Rp ' . number_format($summary->total_pemasukan, 0, ',', '.'),
            'total_pending' => number_format($summary->total_pending, 0, ',', '.'),
            'total_confirmed' => number_format($summary->total_confirmed, 0, ',', '.'),
        ]);
    }

    public function show($id)
    {
        $sale = AppSale::with(['company', 'confirmedBy', 'affiliateCommission'])->findOrFail($id);

        return $this->successResponse('Success', [
            'sale' => $sale,
            'sale_date_formatted' => Carbon::parse($sale->sale_date)->format('d M Y'),
            'confirmed_at_formatted' => $sale->confirmed_at ? Carbon::parse($sale->confirmed_at)->format('d M Y H:i') : null,
        ]);
    }

    public function create()
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/app-sale/form.js',
        ];

        return view('finance.app-sale.form', $data);
    }

    public function edit($id)
    {
        $sale = AppSale::findOrFail($id);

        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/app-sale/form.js',
            'sale' => $sale,
        ];

        return view('finance.app-sale.form', $data);
    }

    public function store(StoreAppSaleRequest $request)
    {
        $validated = $request->validated();
        $process = AppSaleService::store($validated);
        $message = !empty($validated['id']) ? 'Penjualan berhasil diupdate' : 'Penjualan berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function confirm($id)
    {
        $process = AppSaleService::confirm($id);
        return $process ? $this->successResponse('Penjualan berhasil dikonfirmasi') : $this->errorResponse('Gagal mengkonfirmasi penjualan');
    }

    public function destroy(Request $request)
    {
        $process = AppSaleService::destroy($request->id);
        return $process ? $this->successResponse('Penjualan berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
