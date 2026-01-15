<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Model
use App\Models\Role;

class AksesRepository {

    public function __construct(Role $role) {}

    public static function datatable() {
        $query = DB::table('roles')
                    ->select([
                        'roles.id',
                        'roles.name as nama_role',
                        'roles.slug',
                        'roles.status',
                    ])
                    ->where('roles.status', 1);
        
        return $query;
    }

}
