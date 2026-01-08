<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Load Repository
use App\Repositories\Management\UserRepository;

// Load Service
use App\Services\Management\UserService;

// Load Model
use App\Models\Company;
use App\Models\Role;
use App\Models\User;

// Load Request
use App\Http\Requests\Management\User\StoreUserRequest;
use App\Http\Requests\Management\User\UpdateUserRequest;

class UserController extends Controller
{
    private $pageTitle = 'Manajemen User';

    public function index(Request $request)
    {


        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/user/index.js',
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
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/user/form.js',
            'company' => Company::select('kode', 'nama')->get(),
            'paket' => Role::select('slug', 'nama')->get(),
        ];

        return view('management.user.form', $data);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = UserService::store($validated);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user,
        ]);
    }

    public function edit($id)
    {
        $data = [
            'title' => $this->pageTitle,
            'js' => 'resources/js/pages/management/user/form.js',
            'company' => Company::select('kode', 'nama')->get(),
            'paket' => Role::select('slug', 'nama')->get(),
            'user' => User::find($id),
        ];

        return view('management.user.form', $data);
    }

    public function update(UpdateUserRequest $request)
    {
        $validated = $request->validated();

        $user = UserService::update($validated, $request->id);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user,
        ]);
    }

    public function destroy(Request $request)
    {
        $user = User::find($request->id);
        $user->update([
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dihapus',
        ]);
    }

}