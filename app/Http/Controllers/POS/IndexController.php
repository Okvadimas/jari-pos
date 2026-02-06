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
}