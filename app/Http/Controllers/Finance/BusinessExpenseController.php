<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Finance\BusinessExpenseService;
use App\Models\BusinessExpense;
use App\Http\Requests\Finance\BusinessExpense\StoreBusinessExpenseRequest;

class BusinessExpenseController extends Controller
{
    private $pageTitle = 'Pengeluaran Bisnis';

    public function index()
    {
        $data = [
            'startDate' => Carbon::now()->startOfMonth()->format('d/m/Y'),
            'endDate' => Carbon::now()->endOfMonth()->format('d/m/Y'),
            'title' => $this->pageTitle,
            'css' => 'resources/css/pages/finance/business-expense/index.css',
            'js' => 'resources/js/pages/finance/business-expense/index.js',
        ];

        return view('finance.business-expense.index', $data);
    }

    public function datatable(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        return BusinessExpenseService::datatable($startDate, $endDate);
    }

    public function summary(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $summary = BusinessExpenseService::getSummary($startDate, $endDate);

        return $this->successResponse('Success', [
            'total_transaksi' => number_format($summary->total_transaksi, 0, ',', '.'),
            'total_pengeluaran' => 'Rp ' . number_format($summary->total_pengeluaran, 0, ',', '.'),
            'total_server' => 'Rp ' . number_format($summary->total_server, 0, ',', '.'),
            'total_production' => 'Rp ' . number_format($summary->total_production, 0, ',', '.'),
        ]);
    }

    public function show($id)
    {
        $expense = BusinessExpense::with('company')->findOrFail($id);

        return $this->successResponse('Success', [
            'expense' => $expense,
            'expense_date_formatted' => Carbon::parse($expense->expense_date)->format('d M Y'),
        ]);
    }

    public function create()
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/business-expense/form.js',
        ];

        return view('finance.business-expense.form', $data);
    }

    public function edit($id)
    {
        $expense = BusinessExpense::findOrFail($id);

        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/finance/business-expense/form.js',
            'expense' => $expense,
        ];

        return view('finance.business-expense.form', $data);
    }

    public function store(StoreBusinessExpenseRequest $request)
    {
        $validated = $request->validated();
        $process = BusinessExpenseService::store($validated);
        $message = !empty($validated['id']) ? 'Pengeluaran berhasil diupdate' : 'Pengeluaran berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = BusinessExpenseService::destroy($request->id);
        return $process ? $this->successResponse('Pengeluaran berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
