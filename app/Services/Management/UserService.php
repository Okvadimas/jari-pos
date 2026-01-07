<?php

namespace App\Services\Management;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Load Repository
use App\Repositories\Management\UserRepository;

// Load Models
use App\Models\User;

class UserService
{

    public static function datatable()
    {
        $data = UserRepository::datatable();

        $datatable = DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="' . url('management/user/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);

        return $datatable;
    }

    public static function store($validated)
    {
        $user = User::create($validated);
        return $user;
    }

    public static function update($validated, $id)
    {
        $user = User::find($id);
        $user->update($validated);
        return $user;
    }

}