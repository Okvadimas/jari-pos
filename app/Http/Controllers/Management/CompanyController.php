<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Management\CompanyService;

// Load Request
use App\Http\Requests\Management\Company\StoreCompanyRequest;
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

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/company/form.js',
            'company' => Company::find($id),
        ];

        return view('management.company.form', $data);
    }

    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();

        $process = CompanyService::store($validated);

        $message = !empty($validated['id']) ? 'Perusahaan berhasil diupdate' : 'Perusahaan berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = CompanyService::destroy($request->id);

        return $process ? $this->successResponse('Perusahaan berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}
