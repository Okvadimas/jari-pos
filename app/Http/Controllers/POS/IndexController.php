<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Load Service
use App\Services\PosService;
use App\Http\Requests\POS\StorePosRequest;
use App\Models\SalesOrder;

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
        return $this->printReceipt(1);
    }

    public function testReceipt2()
    {
        return $this->printReceipt2(1);
    }

    public function printReceipt($id)
    {
        // Fetch Order Header
        $order = DB::table('sales_orders as so')
                    ->join('companies as c', 'so.company_id', '=', 'c.id')
                    ->join('users as u', 'so.created_by', '=', 'u.id')
                    ->leftJoin('payment_methods as pm', 'so.payment_method_id', '=', 'pm.id')
                    ->leftJoin('promotions as pr', 'so.applied_promo_id', '=', 'pr.id')
                    ->select('so.*', 'c.name as company_name', 'c.address as company_address', 'u.name as created_by_name', 'pm.name as payment_method_name', 'pr.name as promo_name')
                    ->where('so.id', $id)
                    ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Fetch Order Details
        $details = DB::table('sales_order_details as sod')
                    ->join('product_variants as pv', 'sod.product_variant_id', '=', 'pv.id')
                    ->join('products as p', 'pv.product_id', '=', 'p.id')
                    ->select('sod.*', 'p.name as product_name', 'pv.name as variant_name')
                    ->where('sod.sales_order_id', $id)
                    ->get();
        
        // Pass both to view
        return view('pos.receipt', compact('order', 'details'));
    }
    
    public function printReceipt2($id)
    {
        // Fetch Order Header
        $order = DB::table('sales_orders as so')
                    ->join('companies as c', 'so.company_id', '=', 'c.id')
                    ->join('users as u', 'so.created_by', '=', 'u.id')
                    ->leftJoin('payment_methods as pm', 'so.payment_method_id', '=', 'pm.id')
                    ->leftJoin('promotions as pr', 'so.applied_promo_id', '=', 'pr.id')
                    ->select('so.*', 'c.name as company_name', 'c.address as company_address', 'u.name as created_by_name', 'pm.name as payment_method_name', 'pr.name as promo_name')
                    ->where('so.id', $id)
                    ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Fetch Order Details
        $details = DB::table('sales_order_details as sod')
                    ->join('product_variants as pv', 'sod.product_variant_id', '=', 'pv.id')
                    ->join('products as p', 'pv.product_id', '=', 'p.id')
                    ->select('sod.*', 'p.name as product_name', 'pv.name as variant_name')
                    ->where('sod.sales_order_id', $id)
                    ->get();
        
        // Pass both to view
        return view('pos.receipt-2', compact('order', 'details'));
    }
}