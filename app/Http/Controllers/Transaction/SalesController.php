<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Services\Transaction\SalesService;
use App\Models\SalesOrder;
use App\Http\Requests\Transaction\StoreSalesRequest;

class SalesController extends Controller
{
    private $pageTitle = 'Laporan Penjualan';

    public function index(Request $request)
    {
        $startDate  = Carbon::now()->startOfMonth()->format('d/m/Y');
        $endDate    = Carbon::now()->endOfMonth()->format('d/m/Y');

        $data = [
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/transaction/sales/index.js',
        ];

        return view('transaction.sales.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate  = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate    = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return SalesService::datatable($startDate, $endDate);
    }

    public function summary(Request $request)
    {
        $startDate  = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate    = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $summary = SalesService::getSummary($startDate, $endDate);

        return response()->json([
            'total_transaksi' => number_format($summary->total_transaksi, 0, ',', '.'),
            'total_penjualan' => 'Rp ' . number_format($summary->total_penjualan, 0, ',', '.'),
            'total_diskon' => 'Rp ' . number_format($summary->total_diskon, 0, ',', '.'),
            'total_pendapatan' => 'Rp ' . number_format($summary->total_pendapatan, 0, ',', '.'),
        ]);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with(['company', 'details.variant.product', 'appliedPromo'])->findOrFail($id);
        
        // Format response data
        $details = $salesOrder->details->map(function($detail) {
            $productName = optional($detail->variant->product)->name ?? '-';
            $variantName = optional($detail->variant)->name ?? '';

            return [
                'product_name' => trim($productName . ' ' . $variantName),
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'discount' => $detail->discount_auto_amount,
                'subtotal' => $detail->subtotal
            ];
        });

        return response()->json([
            'sales_order' => $salesOrder,
            'customer_name' => $salesOrder->customer_name ?: (optional($salesOrder->company)->name ?? 'Guest'),
            'order_date_formatted' => Carbon::parse($salesOrder->order_date)->format('d M Y H:i'),
            'promo_name' => optional($salesOrder->appliedPromo)->name,
            'details' => $details
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $salesOrder = SalesOrder::with(['details.variant.product'])->findOrFail($id);

        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/transaction/sales/form.js',
            'salesOrder' => $salesOrder,
        ];

        return view('transaction.sales.form', $data);
    }

    /**
     * Store or update sales order
     */
    public function store(StoreSalesRequest $request)
    {
        $validated = $request->validated();

        $process = SalesService::store($validated);

        $message = !empty($validated['id']) ? 'Penjualan berhasil diupdate' : 'Penjualan berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    /**
     * Soft delete sales order
     */
    public function destroy(Request $request)
    {
        $process = SalesService::destroy($request->id);

        return $process ? $this->successResponse('Penjualan berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
