<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\PosService;

class IndexController extends Controller
{
    public function index()
    {
        return view('pos.index');
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

    public function store() {
        
    }
}