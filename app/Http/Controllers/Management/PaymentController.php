<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Management\PaymentService;

// Load Request
use App\Http\Requests\Management\Payment\StorePaymentRequest;
use App\Http\Requests\Management\Payment\UpdatePaymentRequest;
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

    public function store(StorePaymentRequest $request)
    {
        $validated = $request->validated();

        $payment = PaymentService::store($validated);
        if (!$payment) {
            return $this->errorResponse('Pembayaran gagal ditambahkan');
        }

        return $this->successResponse('Pembayaran berhasil ditambahkan', $payment);
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

    public function update(UpdatePaymentRequest $request)
    {
        $validated = $request->validated();

        $payment = PaymentService::update($validated, $request->id);
        if (!$payment) {
            return $this->errorResponse('Pembayaran gagal diupdate');
        }

        return $this->successResponse('Pembayaran berhasil diupdate', $payment);
    }

    public function destroy(Request $request)
    {
        $payment = Payment::find($request->id);
        if ($payment) {
             // Hard delete or Soft delete depending on requirements (using delete for now as per plan/pattern usually implies usage of status or delete)
             // However, controller base usually has destroy logic.
             // Let's use delete() as standard or update status if that's the pattern. 
             // Company uses status update. I'll use delete for now as it's cleaner for simple tables, but let's check Company again.
             // CompanyController uses: $company->update(['status' => 0...])
             
             // For Payment Methods, usually we want to keep history so soft delete or status inactive is better.
             // I will use delete() for now since I didn't add SoftDeletes trait.
             // Wait, Company had status column. Payment has status column too.
             // Let's stick to simple delete for payment method or update status if referenced?
             // The plan said "Delete Payment". 
             
             $payment->delete();
        } else {
            return $this->errorResponse('Pembayaran gagal dihapus');
        }

        return $this->successResponse('Pembayaran berhasil dihapus', $payment);
    }
}
