<?php

namespace App\Services\Affiliator;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Affiliator\AffiliateCommissionRepository;
use App\Models\AffiliateCommission;

class AffiliateCommissionService
{
    public static function datatable($startDate, $endDate)
    {
        $data = AffiliateCommissionRepository::datatable($startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('sale_date', function ($row) {
                return $row->sale_date ? Carbon::parse($row->sale_date)->format('d M Y') : '-';
            })
            ->editColumn('sale_amount', function ($row) {
                return 'Rp ' . number_format($row->sale_amount, 0, ',', '.');
            })
            ->editColumn('commission_amount', function ($row) {
                return 'Rp ' . number_format($row->commission_amount, 0, ',', '.');
            })
            ->editColumn('commission_rate', function ($row) {
                return $row->commission_rate . '%';
            })
            ->editColumn('status', function ($row) {
                $labels = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'paid' => '<span class="badge bg-success">Dibayar</span>',
                    'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>',
                ];
                return $labels[$row->status] ?? '<span class="badge bg-secondary">' . $row->status . '</span>';
            })
            ->addColumn('renewal_badge', function ($row) {
                return $row->is_renewal ? '<span class="badge bg-info">Perpanjangan</span>' : '<span class="badge bg-primary">Baru</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '<button class="btn btn-dim btn-sm btn-outline-info" onclick="detail(' . $row->id . ')"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</button> ';
                if ($row->status === 'pending') {
                    $actions .= '<button class="btn btn-dim btn-sm btn-outline-success" onclick="bayar(' . $row->id . ')"><em class="icon ni ni-wallet-fill d-none d-sm-inline me-1"></em> Bayar</button> ';
                }
                return $actions;
            })
            ->rawColumns(['action', 'status', 'renewal_badge'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return AffiliateCommissionRepository::getSummary($startDate, $endDate);
    }

    /**
     * Mark commission as paid
     */
    public static function markAsPaid($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = Auth::user();
                $commission = AffiliateCommission::lockForUpdate()->find($id);

                if (!$commission || $commission->status !== 'pending') return false;

                $commission->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                    'updated_by' => $user->id,
                ]);

                return $commission;
            });
        } catch (\Throwable $th) {
            Log::error('AffiliateCommissionService::markAsPaid - ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Cancel commission
     */
    public static function cancel($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = Auth::user();
                $commission = AffiliateCommission::lockForUpdate()->find($id);

                if (!$commission || $commission->status !== 'pending') return false;

                $commission->update([
                    'status' => 'cancelled',
                    'updated_by' => $user->id,
                ]);

                return $commission;
            });
        } catch (\Throwable $th) {
            Log::error('AffiliateCommissionService::cancel - ' . $th->getMessage());
            return false;
        }
    }
}
