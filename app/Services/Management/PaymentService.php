<?php

namespace App\Services\Management;

use App\Repositories\Management\PaymentRepository;
use App\Models\Payment;
use Yajra\DataTables\Facades\DataTables;

class PaymentService {

    public static function datatable() {
        $query = PaymentRepository::datatable();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                if ($row->status) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">Inactive</span>';
                }
            })
            ->editColumn('type', function ($row) {
                return ucfirst(str_replace('_', ' ', $row->type));
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0)" onclick="editData('.$row->id.')" class="btn btn-primary btn-sm me-2"><i class="fas fa-edit"></i> Edit</a>';
                $btn .= '<a href="javascript:void(0)" onclick="deleteData('.$row->id.')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public static function store($data) {
        $data['created_by'] = auth()->user()->id;
        $data['updated_by'] = auth()->user()->id;

        return Payment::create($data);
    }

    public static function update($data, $id) {
        $data['updated_by'] = auth()->user()->id;
        $payment = Payment::find($id);
        $payment->update($data);

        return $payment;
    }
}
