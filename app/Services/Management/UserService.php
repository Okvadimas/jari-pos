<?php

namespace App\Services\Management;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        $validated['company_id'] = $validated['company'];
        $validated['role_id']    = $validated['paket'];

        // Jika ada id, update. Jika tidak, create.
        if (!empty($validated['id'])) {
            $user = User::find($validated['id']);
            $user->update($validated);
        } else {
            $validated['password']   = Hash::make('12345');
            $validated['start_date'] = date('Y-m-d H:i:s');
            $validated['end_date']   = date('Y-m-d H:i:s', strtotime('+1 month'));
            $validated['status']     = 1;
            $user = User::create($validated);
        }

        return $user;
    }

    public static function destroy($id)
    {
        $user = User::find($id);
        $user->update([
            'status'        => '0',
            'updated_by'    => Auth::user()->id,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

}