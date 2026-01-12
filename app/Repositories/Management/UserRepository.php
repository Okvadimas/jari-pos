<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserRepository {

    public function __construct(User $user) {}

    public static function datatable() {
        $query = DB::table('users')
                    ->select([
                        'users.id',
                        'c.nama as nama_company',
                        'users.company',
                        'users.name',
                        'r.nama as nama_role',
                        'users.role',
                        'users.status',
                    ])
                    ->join('company as c', 'users.company', '=', 'c.kode')
                    ->join('role as r', 'users.role', '=', 'r.slug')
                    ->where('users.status', 'active');
        
        return $query;
    }

}
