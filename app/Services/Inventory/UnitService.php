<?php

namespace App\Services\Inventory;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Inventory\UnitRepository;

class UnitService
{
    public static function datatable()
    {
        $data = UnitRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if(!$row->status) {
                    return '';
                }
                return '<a href="' . url('inventory/unit/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->editColumn('status', function ($row) {
                return $row->status == 1 ? 'Active' : 'Inactive';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public static function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = [
                'code' => $request['code'],
                'name' => $request['name'],
                'created_by' => auth()->user()->id,
            ];

            return Unit::create($data);
        });
    }

    public static function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $unit = Unit::findOrFail($id);
            
            $data = [
                'code' => $request['code'],
                'name' => $request['name'],
                'updated_by' => auth()->user()->id,
            ];

            $unit->update($data);

            return $unit;
        });
    }
}
