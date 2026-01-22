<?php

namespace App\Services\Management;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        try {
            DB::beginTransaction();

            $data = [
                'name'      => $request['name'],
                'email'     => $request['email'],
                'phone'     => $request['phone'] ?? null,
                'address'   => $request['address'] ?? null,
            ];

            if (isset($request['logo'])) {
                $file = $request['logo'];
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/companies', $filename);
                $data['logo'] = $filename;
            }

            if (!empty($request['id'])) {
                $company = Company::find($request['id']);

                // Jika ada logo baru dan sebelumnya sudah ada logo, hapus logo lama
                if (isset($request['logo']) && $company->logo) {
                    Storage::delete('public/companies/' . $company->logo);
                }

                $data['updated_by'] = Auth::user()->id;
                $company->update($data);
            } else {
                $data['created_by'] = Auth::user()->id;
                $company = Company::create($data);
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return false;
        }
    }

}
