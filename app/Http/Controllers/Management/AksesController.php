<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Load Model
use App\Models\Menu;
use App\Models\Role;
use App\Models\Permission;

// Load Service
use App\Services\Management\AksesService;

// Load Request
use App\Http\Requests\Management\Akses\StoreAksesRequest;

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
            'js'        => 'resources/js/pages/management/akses/form.js',
            'menus'     => Menu::select('menu.id', 'menu.code', 'menu.parent', 'menu.name', 'parent.name as parent_name')
                            ->join('menu as parent', 'menu.parent', 'parent.code')
                            ->where('menu.parent', '!=', '0')
                            ->where('menu.status', 1)
                            ->get(),
            'permissions' => Permission::select('menu_id')->where('status', 1)->where('role_id', $id)->get(),
            'akses'     => Role::find($id),
        ];

        return view('management.akses.form', $data);
    }

    public function store(StoreAksesRequest $request)
    {
        $validated = $request->validated();

        AksesService::store($validated);

        $message = !empty($validated['id']) ? 'Akses berhasil diupdate' : 'Akses berhasil ditambahkan';

        return $this->successResponse($message);
    }

    public function destroy(Request $request)
    {
        AksesService::destroy($request->id);

        return $this->successResponse('Akses berhasil dihapus');
    }
}