<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Load Service
use App\Services\Management\UserService;

// Load Model
use App\Models\Company;
use App\Models\Role;
use App\Models\User;

// Load Request
use App\Http\Requests\Management\User\StoreUserRequest;

class UserController extends Controller
{
    private $pageTitle = 'Manajemen User';

    public function index()
    {
        $data = [
            'title' => $this->pageTitle,
            'js'    => 'resources/js/pages/management/user/index.js',
        ];

        return view('management.user.index', $data);
    }

    public function datatable()
    {
        return UserService::datatable();
    }

    public function create(Request $request)
    {
        $data = [
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/management/user/form.js',
            'company'   => Company::select('id', 'name')->get(),
            'paket'     => Role::select('id', 'name')->get(),
        ];

        return view('management.user.form', $data);
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

        UserService::store($validated);

        $message = !empty($validated['id']) ? 'User berhasil diupdate' : 'User berhasil ditambahkan';

        return $this->successResponse($message);
    }

    public function destroy(Request $request)
    {
        UserService::destroy($request->id);

        return $this->successResponse('User berhasil dihapus');
    }

}