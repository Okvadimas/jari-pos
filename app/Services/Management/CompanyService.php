<?php

namespace App\Services\Management;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CompanyService
{
    public static function datatable()
    {
        $data = Company::select('id', 'name', 'email', 'phone', 'address', 'status')->where('status', 1)->orderBy('id', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<div class="drodown">
                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="link-list-opt no-bdr">
                                    <li><a href="' . route('company-management-edit', $row->id) . '"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                    <li><a href="#" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash"></em><span>Hapus</span></a></li>
                                </ul>
                            </div>
                        </div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = [
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'created_by' => auth()->user()->id,
            ];

            if (isset($request['logo'])) {
                $file = $request['logo'];
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/companies', $filename);
                $data['logo'] = $filename;
            }

            return Company::create($data);
        });
    }

    public static function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $company = Company::findOrFail($id);
            
            $data = [
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'updated_by' => auth()->user()->id,
            ];

            if (isset($request['logo'])) {
                if ($company->logo) {
                    Storage::delete('public/companies/' . $company->logo);
                }

                $file = $request['logo'];
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/companies', $filename);
                $data['logo'] = $filename;
            }

            $company->update($data);

            return $company;
        });
    }
}

<?php

namespace App\Services\Management;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CompanyService
{
    public static function datatable()
    {
        $data = Company::select('id', 'kode', 'nama', 'email', 'telepon', 'alamat', 'status')->orderBy('id', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<div class="drodown">
                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="link-list-opt no-bdr">
                                    <li><a href="' . route('company-management-edit', $row->id) . '"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                    <li><a href="#" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash"></em><span>Hapus</span></a></li>
                                </ul>
                            </div>
                        </div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function store($request)
    {
        return DB::transaction(function () use ($request) {
            // Generate Kode Company (Simple Unique)
            $lastCompany = Company::orderBy('id', 'desc')->first();
            $nextId = $lastCompany ? $lastCompany->id + 1 : 1;
            $kode = 'CMP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $data = [
                'kode' => $kode,
                'nama' => $request['nama'],
                'email' => $request['email'],
                'telepon' => $request['telepon'],
                'alamat' => $request['alamat'],
                'status' => 'active', // Default active
                'created_by' => auth()->user()->id,
            ];

            if (isset($request['logo'])) {
                // Handle file upload if needed, for now just store string/path if logic exists
                // $data['logo'] = $request['logo'];
            }

            return Company::create($data);
        });
    }

    public static function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $company = Company::findOrFail($id);
            
            $data = [
                'nama' => $request['nama'],
                'email' => $request['email'],
                'telepon' => $request['telepon'],
                'alamat' => $request['alamat'],
                'updated_by' => auth()->user()->id,
            ];

             if (isset($request['status'])) {
                $data['status'] = $request['status'];
            }

            $company->update($data);

            return $company;
        });
    }
}
