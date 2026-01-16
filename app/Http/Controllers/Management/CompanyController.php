<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Management\CompanyService;

// Load Request
use App\Http\Requests\Management\Company\StoreCompanyRequest;
use App\Http\Requests\Management\Company\UpdateCompanyRequest;
use App\Models\Company;

class CompanyController extends Controller
{
    private $pageTitle = 'Manajemen Perusahaan';

    public function index(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/company/index.js',
        ];

        return view('management.company.index', $data);
    }

    public function datatable()
    {
        return CompanyService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/company/form.js',
        ];

        return view('management.company.form', $data);
    }

    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();

        $company = CompanyService::store($validated);
        if (!$company) {
            return $this->errorResponse('Perusahaan gagal ditambahkan');
        }

        return $this->successResponse('Perusahaan berhasil ditambahkan', $company);
    }

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/company/form.js',
            'company' => Company::find($id),
        ];

        return view('management.company.form', $data);
    }

    public function update(UpdateCompanyRequest $request)
    {
        $validated = $request->validated();

        $company = CompanyService::update($validated, $request->id);
        if (!$company) {
            return $this->errorResponse('Perusahaan gagal diupdate');
        }

        return $this->successResponse('Perusahaan berhasil diupdate', $company);
    }

    public function destroy(Request $request)
    {
        $company = Company::find($request->id);
        if ($company) {
             $company->update([
                'status' => 0,
                'updated_by' => auth()->user()->id,
             ]);
        } else {
            return $this->errorResponse('Perusahaan gagal dihapus');
        }

        return $this->successResponse('Perusahaan berhasil dihapus', $company);
    }
}
