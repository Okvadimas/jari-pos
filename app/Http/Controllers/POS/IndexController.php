<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\PosService;
use App\Http\Requests\POS\StorePosRequest;

class IndexController extends Controller
{
    public function index()
    {
        $payments = PosService::getPaymentMethods();
        return view('pos.index', compact('payments'));
    }

    public function getProducts(Request $request)
    {
        $products = PosService::getProducts($request);
        return $this->successResponse('Data berhasil diambil', $products);
    }

    public function getCategories()
    {
        $categories = PosService::getCategories();
        return $this->successResponse('Data berhasil diambil', $categories);
    }

    public function getTopSelling()
    {
        $topSelling = PosService::getTopSelling();
        return $this->successResponse('Data berhasil diambil', $topSelling);
    }

    public function getVouchers()
    {
        $vouchers = PosService::getVouchers();
        return $this->successResponse('Data berhasil diambil', $vouchers);
    }

    public function store(StorePosRequest $request) {
        $validated = $request->validated();
        $process = PosService::store($validated);

        if ($process['status']) {
            return $this->successResponse('Transaksi berhasil disimpan', $process['data']);
        } else {
            return $this->errorResponse($process['message'] ?? 'Terjadi kesalahan saat menyimpan transaksi');
        }
    }

    public function testReceipt()
    {
        return $this->printReceipt(request(), 1);
    }

    public function testReceipt2()
    {
        return $this->printReceipt2(1);
    }

    public function printReceipt(Request $request, $id)
    {
        $data = PosService::getOrderWithDetails($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        $order = $data['order'];
        $details = $data['details'];
        $paperSize = $request->query('size', '80');

        return view('pos.receipt', compact('order', 'details', 'paperSize'));
    }
    
    /**
     * Get receipt data as JSON for thermal printer
     */
    public function getReceiptData(Request $request, $id)
    {
        $data = PosService::getOrderWithDetails($id);

        if (!$data) {
            return $this->errorResponse('Order tidak ditemukan');
        }

        return $this->successResponse('Data struk berhasil diambil', $data);
    }

    /**
     * Get transaction history for the POS history modal
     */
    public function getTransactionHistory(Request $request)
    {
        $transactions = PosService::getTransactionHistory($request);
        return $this->successResponse('Data riwayat transaksi', $transactions);
    }

    public function printReceipt2($id)
    {
        $data = PosService::getOrderWithDetails($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        $order = $data['order'];
        $details = $data['details'];
        
        return view('pos.receipt-2', compact('order', 'details'));
    }
}