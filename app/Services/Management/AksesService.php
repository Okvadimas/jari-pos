<?php

namespace App\Services\Management;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

// Load Repository
use App\Repositories\Management\AksesRepository;

// Load Models
use App\Models\Role;
use App\Models\Permission;

class AksesService {     
    public static function datatable()
    {
        $data = AksesRepository::datatable();

        $datatable = DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="' . url('management/akses/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);

        return $datatable;
    }

    public static function store($validated)
    {
        try {
            DB::beginTransaction();

            if (!empty($validated['id'])) {
                $akses = Role::find($validated['id']);
                $akses->update($validated);
            } else {
                $akses = Role::create($validated);
            }

            // Delete Insert Permissions
            Permission::where('role_id', $akses->id)->delete();

            $data_permission = [];
            foreach ($validated['menus'] as $menu) {
                $data_permission[] = [
                    'role_id'       => $akses->id,
                    'menu_id'       => $menu,
                    'status'        => 1,
                    'created_by'    => Auth::user()->id,
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
            }

            Permission::insert($data_permission);

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
        $akses = Role::find($id);
        $akses->update([
            'status'        => '0',
            'updated_by'    => Auth::user()->id,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}