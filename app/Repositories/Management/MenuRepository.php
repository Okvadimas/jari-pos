<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;

// Load Model
use App\Models\Menu;

class MenuRepository {

    public function __construct(Menu $menu) {}

    public static function getMenuHeaders($roleId) {
        // 1. Get Parents of accessible children
        $parents = DB::table('menu as p')
            ->join('menu as c', 'c.parent', '=', 'p.code')
            ->join('permissions as perm', 'perm.menu_id', '=', 'c.id')
            ->join('roles as r', 'r.id', '=', 'perm.role_id')
            ->where('perm.role_id', $roleId)
            ->where('perm.status', 1)
            ->where('p.status', 1)
            ->where('r.status', 1)
            ->select('p.id', 'p.parent', 'p.code', 'p.name', 'p.icon', 'p.url')
            ->distinct();

        // 2. Get Accessible Roots (Union with Parents)
        return DB::table('menu as m')
            ->join('permissions as perm', 'perm.menu_id', '=', 'm.id')
            ->join('roles as r', 'r.id', '=', 'perm.role_id')
            ->where('perm.role_id', $roleId)
            ->where('perm.status', 1)
            ->where('m.parent', '0')
            ->where('m.status', 1)
            ->where('r.status', 1)
            ->select('m.id', 'm.parent', 'm.code', 'm.name', 'm.icon', 'm.url')
            ->union($parents)
            ->orderBy('id')
            ->get();
    }

    public static function getMenuChildren($roleId, $parentCode) {
        return DB::table('menu as c')
            ->join('permissions as perm', 'perm.menu_id', '=', 'c.id')
            ->join('roles as r', 'r.id', '=', 'perm.role_id')
            ->where('perm.role_id', $roleId)
            ->where('perm.status', 1)
            ->where('c.parent', $parentCode)
            ->where('c.status', 1)
            ->where('r.status', 1)
            ->select('c.id', 'c.parent', 'c.code', 'c.name', 'c.url', 'c.icon')
            ->orderBy('c.id')
            ->get();
    }
}
