<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Load Model
use App\Models\Role;

class PermissionRepository {

    public function __construct(Role $role) {}

    public static function datatable() {
        $query = DB::table('roles')
                    ->whereNull('deleted_at')
                    ->select([
                        'roles.id',
                        'roles.name as nama_role',
                        'roles.slug',
                    ]);
        
        return $query;
    }

}
