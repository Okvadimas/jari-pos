<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Load Model
use App\Models\Menu;
use App\Models\Role;
use App\Models\Permission;

// Load Service
use App\Services\Management\PermissionService;

// Load Request
use App\Http\Requests\Management\Permission\StorePermissionRequest;

class PermissionController extends Controller
{
    private $pageTitle = 'Manajemen Akses';
    public function index()
    {
        $data = [
            'title' => $this->pageTitle,
            'js'    => 'resources/js/pages/management/permission/index.js',
        ];

        return view('management.permission.index', $data);
    }

    public function datatable()
    {
        return PermissionService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/management/permission/form.js',
            'menus'     => Menu::select('menu.id', 'menu.code', 'menu.parent', 'menu.name', 'parent.name as parent_name')
                            ->join('menu as parent', 'menu.parent', 'parent.code')
                            ->where('menu.parent', '!=', '0')
                            ->whereNull('menu.deleted_at')
                            ->orderBy('menu.id', 'asc')
                            ->get(),
        ];

        return view('management.permission.form', $data);
    }

    public function edit($id)
    {
        $data = [
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/management/permission/form.js',
            'menus'     => Menu::select('menu.id', 'menu.code', 'menu.parent', 'menu.name', 'parent.name as parent_name')
                            ->join('menu as parent', 'menu.parent', 'parent.code')
                            ->where('menu.parent', '!=', '0')
                            ->whereNull('menu.deleted_at')
                            ->orderBy('menu.id', 'asc')
                            ->get(),
            'permissions' => Permission::select('menu_id')->where('role_id', $id)->whereNull('deleted_at')->get(),
            'role'      => Role::find($id),
        ];

        return view('management.permission.form', $data);
    }

    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();

        $process = PermissionService::store($validated);

        $message = !empty($validated['id']) ? 'Akses berhasil diupdate' : 'Akses berhasil ditambahkan';

        return $process ? $this->successResponse($message) : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    public function destroy(Request $request)
    {
        $process = PermissionService::destroy($request->id);

        return $process ? $this->successResponse('Akses berhasil dihapus') : $this->errorResponse('Terjadi kesalahan');
    }
}