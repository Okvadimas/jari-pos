<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\VoucherService;
use App\Models\Voucher;
use App\Http\Requests\Finance\Voucher\StoreVoucherRequest;

class VoucherController extends Controller
{
    private $pageTitle = 'Kupon Diskon';

    public function index()
    {
        $data = [
            'title' => $this->pageTitle,
            'css' => 'resources/css/pages/finance/voucher/index.css',
            'js' => 'resources/js/pages/finance/voucher/index.js',
        ];

        return view('finance.voucher.index', $data);
    }

    public function datatable()
    {
        return VoucherService::datatable();
    }

    public function summary()
    {
        $summary = VoucherService::getSummary();

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
            'js' => 'resources/js/pages/finance/voucher/form.js',
        ];

        return view('finance.voucher.form', $data);
    }

    public function edit($id)
    {
        $coupon = Voucher::findOrFail($id);

        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/voucher/form.js',
            'coupon' => $coupon,
        ];

        return view('finance.voucher.form', $data);
    }

    public function store(StoreVoucherRequest $request)
    {
        $validated = $request->validated();
        $process = VoucherService::store($validated);
        $message = !empty($validated['id']) ? 'Kupon berhasil diupdate' : 'Kupon berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = VoucherService::destroy($request->id);
        return $process ? $this->successResponse('Kupon berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
