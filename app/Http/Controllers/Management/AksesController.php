<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Load Model
use App\Models\Menu;
use App\Models\Role;

// Load Service
use App\Services\Management\AksesService;

class AksesController extends Controller
{
    private $pageTitle = 'Manajemen Akses';
    public function index()
    {
        $data = [
            'title' => $this->pageTitle,
            'js'    => 'resources/js/pages/management/akses/index.js',
        ];

        return view('management.akses.index', $data);
    }

    public function datatable()
    {
        return AksesService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/management/akses/form.js',
            'menus'     => Menu::select('menu.id', 'menu.code', 'menu.parent', 'menu.name', 'parent.name as parent_name')
                            ->join('menu as parent', 'menu.parent', 'parent.code')
                            ->where('menu.parent', '!=', '0')
                            ->where('menu.status', 1)
                            ->get(),
        ];

        return view('management.akses.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/management/user/form.js',
            'company'   => Company::select('id', 'name')->get(),
            'paket'     => Role::select('id', 'name')->get(),
            'user'      => User::find($id),
        ];

        return view('management.user.form', $data);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        AksesService::store($validated);

        $message = !empty($validated['id']) ? 'Akses berhasil diupdate' : 'Akses berhasil ditambahkan';

        return $this->successResponse([], $message);
    }

    public function destroy(Request $request)
    {
        AksesService::destroy($request->id);

        return $this->successResponse([], 'Akses berhasil dihapus');
    }
}