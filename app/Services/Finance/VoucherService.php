<?php

namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Finance\VoucherRepository;
use App\Models\Voucher;

class VoucherService
{
    public static function datatable()
    {
        $data = VoucherRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('value', function ($row) {
                if ($row->type === 'percentage') {
                    return $row->value . '%';
                }
                return 'Rp ' . number_format($row->value, 0, ',', '.');
            })
            ->editColumn('type', function ($row) {
                $labels = [
                    'percentage' => '<span class="badge bg-info">Persentase</span>',
                    'fixed' => '<span class="badge bg-primary">Nominal</span>',
                ];
                return $labels[$row->type] ?? $row->type;
            })
            ->addColumn('usage', function ($row) {
                $max = $row->max_uses ?? '∞';
                return $row->used_count . ' / ' . $max;
            })
            ->addColumn('validity', function ($row) {
                $from = $row->valid_from ? Carbon::parse($row->valid_from)->format('d M Y') : '-';
                $until = $row->valid_until ? Carbon::parse($row->valid_until)->format('d M Y') : '-';
                return $from . ' s/d ' . $until;
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Nonaktif</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . url('finance/voucher/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action', 'type', 'is_active'])
            ->make(true);
    }

    public static function getSummary()
    {
        return VoucherRepository::getSummary();
    }

    public static function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = Auth::user();

                if (!empty($data['id'])) {
                    $coupon = Voucher::lockForUpdate()->find($data['id']);
                    if (!$coupon) throw new \Exception('Kupon tidak ditemukan');

                    $coupon->update([
                        'code' => $data['code'],
                        'name' => $data['name'],
                        'type' => $data['type'],
                        'value' => $data['value'],
                        'max_uses' => $data['max_uses'] ?? null,
                        'valid_from' => !empty($data['valid_from']) ? Carbon::createFromFormat('d/m/Y', $data['valid_from']) : null,
                        'valid_until' => !empty($data['valid_until']) ? Carbon::createFromFormat('d/m/Y', $data['valid_until']) : null,
                        'is_active' => $data['is_active'] ?? true,
                        'updated_by' => $user->id,
                    ]);
                } else {
                    $coupon = Voucher::create([
                        'code' => $data['code'],
                        'name' => $data['name'],
                        'type' => $data['type'],
                        'value' => $data['value'],
                        'max_uses' => $data['max_uses'] ?? null,
                        'valid_from' => !empty($data['valid_from']) ? Carbon::createFromFormat('d/m/Y', $data['valid_from']) : null,
                        'valid_until' => !empty($data['valid_until']) ? Carbon::createFromFormat('d/m/Y', $data['valid_until']) : null,
                        'is_active' => $data['is_active'] ?? true,
                        'created_by' => $user->id,
                    ]);
                }

                return $coupon;
            });
        } catch (\Throwable $th) {
            Log::error('VoucherService::store - ' . $th->getMessage());
            return false;
        }
    }

    public static function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $coupon = Voucher::find($id);
                if (!$coupon) return false;
                $coupon->delete();
                return true;
            });
        } catch (\Throwable $th) {
            Log::error('VoucherService::destroy - ' . $th->getMessage());
            return false;
        }
    }
}
