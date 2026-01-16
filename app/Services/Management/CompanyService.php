<?php

namespace App\Services\Management;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

use App\Repositories\Management\CompanyRepository;

class CompanyService
{
    public static function datatable()
    {
        $data = CompanyRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (!$row->status) {
                    return '';
                }

                return '<a href="' . url('management/company/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
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
