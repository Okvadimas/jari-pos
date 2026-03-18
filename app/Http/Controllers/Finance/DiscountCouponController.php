<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\DiscountCouponService;
use App\Models\DiscountCoupon;
use App\Http\Requests\Finance\DiscountCoupon\StoreDiscountCouponRequest;

class DiscountCouponController extends Controller
{
    private $pageTitle = 'Kupon Diskon';

    public function index()
    {
        $data = [
            'title' => $this->pageTitle,
            'css' => 'resources/css/pages/finance/discount-coupon/index.css',
            'js' => 'resources/js/pages/finance/discount-coupon/index.js',
        ];

        return view('finance.discount-coupon.index', $data);
    }

    public function datatable()
    {
        return DiscountCouponService::datatable();
    }

    public function summary()
    {
        $summary = DiscountCouponService::getSummary();

        return $this->successResponse('Success', [
            'total_kupon' => number_format($summary->total_kupon, 0, ',', '.'),
            'total_aktif' => number_format($summary->total_aktif, 0, ',', '.'),
            'total_digunakan' => number_format($summary->total_digunakan, 0, ',', '.'),
        ]);
    }

    public function create()
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/discount-coupon/form.js',
        ];

        return view('finance.discount-coupon.form', $data);
    }

    public function edit($id)
    {
        $coupon = DiscountCoupon::findOrFail($id);

        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/discount-coupon/form.js',
            'coupon' => $coupon,
        ];

        return view('finance.discount-coupon.form', $data);
    }

    public function store(StoreDiscountCouponRequest $request)
    {
        $validated = $request->validated();
        $process = DiscountCouponService::store($validated);
        $message = !empty($validated['id']) ? 'Kupon berhasil diupdate' : 'Kupon berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = DiscountCouponService::destroy($request->id);
        return $process ? $this->successResponse('Kupon berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
