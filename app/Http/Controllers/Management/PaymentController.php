<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Management\PaymentService;

// Load Request
use App\Http\Requests\Management\Payment\StorePaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
    private $pageTitle = 'Manajemen Pembayaran';

    public function index(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/payment/index.js',
        ];

        return view('management.payment.index', $data);
    }

    public function datatable()
    {
        return PaymentService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/payment/form.js',
        ];

        return view('management.payment.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/payment/form.js',
            'payment' => Payment::find($id),
        ];

        return view('management.payment.form', $data);
    }

    public function store(StorePaymentRequest $request)
    {
        $validated = $request->validated();

        PaymentService::store($validated);

        $message = !empty($validated['id']) ? 'Pembayaran berhasil diupdate' : 'Pembayaran berhasil ditambahkan';

        return $this->successResponse($message);
    }

    public function destroy(Request $request)
    {
        $payment = Payment::find($request->id);
        $payment->delete();

        return $this->successResponse('Pembayaran berhasil dihapus');
    }
}
