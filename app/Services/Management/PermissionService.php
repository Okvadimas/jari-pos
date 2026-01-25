<?php

namespace App\Services\Management;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

// Load Repository
use App\Repositories\Management\PermissionRepository;

// Load Models
use App\Models\Role;
use App\Models\Permission;

class PermissionService {     
    public static function datatable()
    {
        $data = PermissionRepository::datatable();

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
                $role = Role::find($validated['id']);
                $role->update($validated);
            } else {
                $role = Role::create($validated);
            }

            // Delete Insert Permissions
            Permission::where('role_id', $role->id)->forceDelete();

            $data_permission = [];
            foreach ($validated['menus'] as $menu) {
                $data_permission[] = [
                    'role_id'       => $role->id,
                    'menu_id'       => $menu,
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
        try {
            // Delete permissions related to this role first
            Permission::where('role_id', $id)->forceDelete();
            
            // Delete the role
            $role = Role::find($id);
            $role->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return false;
        }
    }

}