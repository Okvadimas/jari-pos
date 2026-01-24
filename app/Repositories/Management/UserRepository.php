<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Model
use App\Models\User;

class UserRepository {

    public function __construct(User $user) {}

    public static function datatable() {
        $query = DB::table('users')
                    ->select([
                        'users.id',
                        'c.name as nama_company',
                        'users.company_id',
                        'users.name',
                        'r.name as nama_role',
                        'users.role_id',
                    ])
                    ->join('companies as c', 'users.company_id', '=', 'c.id')
                    ->join('roles as r', 'users.role_id', '=', 'r.id')
                    ->whereNull('users.deleted_at')
                    ->whereNull('c.deleted_at')
                    ->whereNull('r.deleted_at');
        
        return $query;
    }

}
