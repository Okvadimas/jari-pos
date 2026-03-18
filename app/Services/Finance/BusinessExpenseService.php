<?php

namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Finance\BusinessExpenseRepository;
use App\Models\BusinessExpense;
use App\Services\Utilities\TransactionNumberService;

class BusinessExpenseService
{
    public static function datatable($startDate, $endDate)
    {
        $data = BusinessExpenseRepository::datatable($startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('expense_date', function ($row) {
                return Carbon::parse($row->expense_date)->format('d M Y');
            })
            ->editColumn('amount', function ($row) {
                return 'Rp ' . number_format($row->amount, 0, ',', '.');
            })
            ->editColumn('category', function ($row) {
                $labels = [
                    'server' => '<span class="badge bg-info">Server</span>',
                    'production' => '<span class="badge bg-warning">Produksi</span>',
                    'other' => '<span class="badge bg-secondary">Lainnya</span>',
                ];
                return $labels[$row->category] ?? '<span class="badge bg-secondary">' . ucfirst($row->category) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . url('finance/business-expense/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action', 'category'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return BusinessExpenseRepository::getSummary($startDate, $endDate);
    }

    public static function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = Auth::user();
                $expenseDate = Carbon::createFromFormat('d/m/Y', $data['expense_date']);

                if (!empty($data['id'])) {
                    $expense = BusinessExpense::lockForUpdate()->find($data['id']);
                    if (!$expense) {
                        throw new \Exception('Data pengeluaran tidak ditemukan');
                    }

                    $expense->update([
                        'category' => $data['category'],
                        'description' => $data['description'],
                        'amount' => $data['amount'],
                        'expense_date' => $expenseDate,
                        'vendor_name' => $data['vendor_name'] ?? null,
                        'reference_note' => $data['reference_note'] ?? null,
                        'updated_by' => $user->id,
                    ]);
                } else {
                    $expenseNumber = TransactionNumberService::generateBusinessExpense($user->company_id, $expenseDate);

                    $expense = BusinessExpense::create([
                        'expense_number' => $expenseNumber,
                        'company_id' => $user->company_id,
                        'category' => $data['category'],
                        'description' => $data['description'],
                        'amount' => $data['amount'],
                        'expense_date' => $expenseDate,
                        'vendor_name' => $data['vendor_name'] ?? null,
                        'reference_note' => $data['reference_note'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }

                return $expense;
            });
        } catch (\Throwable $th) {
            Log::error('BusinessExpenseService::store - ' . $th->getMessage());
            return false;
        }
    }

    public static function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $expense = BusinessExpense::find($id);
                if (!$expense) return false;
                $expense->delete();
                return true;
            });
        } catch (\Throwable $th) {
            Log::error('BusinessExpenseService::destroy - ' . $th->getMessage());
            return false;
        }
    }
}
