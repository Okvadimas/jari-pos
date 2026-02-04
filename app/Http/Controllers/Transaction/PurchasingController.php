<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Services\Transaction\PurchasingService;
use App\Models\Purchase;

class PurchasingController extends Controller
{
    private $pageTitle = 'Laporan Pembelian';

    public function index(Request $request)
    {
        $startDate  = Carbon::now()->startOfMonth()->format('d/m/Y');
        $endDate    = Carbon::now()->endOfMonth()->format('d/m/Y');

        $data = [
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/transaction/purchasing/index.js',
        ];

        return view('transaction.purchasing.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate  = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate    = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return PurchasingService::datatable($startDate, $endDate);
    }

    public function summary(Request $request)
    {
        $startDate  = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate    = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $summary = PurchasingService::getSummary($startDate, $endDate);

        return response()->json([
            'total_transaksi' => number_format($summary->total_transaksi, 0, ',', '.'),
            'total_pembelian' => 'Rp ' . number_format($summary->total_pembelian, 0, ',', '.'),
        ]);
    }

    public function show($id)
    {
        $purchase = Purchase::with(['company', 'details.variant.product'])->findOrFail($id);
        
        // Format response data
        $details = $purchase->details->map(function($detail) {
            $productName = optional($detail->variant->product)->name ?? '-';
            $variantName = optional($detail->variant)->name ?? '';
            
            return [
                'product_name' => trim($productName . ' ' . $variantName),
                'sku' => optional($detail->variant)->sku ?? '-',
                'quantity' => $detail->quantity,
                'cost' => $detail->cost_price_per_item,
                'total' => $detail->quantity * $detail->cost_price_per_item
            ];
        });

        return response()->json([
            'purchase' => $purchase,
            'company_name' => $purchase->company ? $purchase->company->name : $purchase->supplier_name,
            'purchase_date_formatted' => Carbon::parse($purchase->purchase_date)->format('d M Y H:i'),
            'details' => $details
        ]);
    }
}

