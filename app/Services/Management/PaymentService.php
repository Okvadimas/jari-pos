<?php

namespace App\Services\Management;

use App\Repositories\Management\PaymentRepository;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PaymentService {

    public static function datatable() {
        $query = PaymentRepository::datatable();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('type', function ($row) {
                return ucfirst(str_replace('_', ' ', $row->type));
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . url('management/payment/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function store($data) {
        try {
            DB::beginTransaction();

            if (!empty($data['id'])) {
                $payment = Payment::find($data['id']);
                $data['updated_by'] = Auth::user()->id;
                $payment->update($data);
            } else {
                $data['created_by'] = Auth::user()->id;
                $payment = Payment::create($data);
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return false;
        }
    }

    public static function destroy($id)
    {
        try {
            $payment = Payment::find($id);
            $payment->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return false;
        }
    }

}
