<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = [
        'id',
        'code',
        'parent',
        'name',
        'icon',
        'url',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_by'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'menu_id');
    }

    public function dataTableMenu() {
        return self::select('id', 'code', 'parent', 'name', 'icon', 'url', 'status')->where('status', 1);
    }

    public function viewMenuTemplate($parent = '0', $level = '0', $role = '') {

        if (empty($role)) {
            $result = $this->menuTemplate($parent);
        } else {
            $result = $this->menuTemplateByRole($parent, $role);
        }

        $arr = array();

        if (!empty($result)) {
            foreach ($result as $row => $val) {
                $id_menu = $val->code;

                if (empty($role)) {
                    // Semua action, default-nya unchecked saat akan membuat role baru.
                    $state_readOnly     = '';
                    $state_fullAccess   = '';
                    $state_noAccess     = '';
                } else {
                    $state_readOnly     = ($val->status == 0) ? 'checked' : '';
                    $state_fullAccess   = ($val->status == 1) ? 'checked' : '';
                    $state_noAccess     = ($val->status == 9) ? 'checked' : '';
                }

                $id_readOnly    = 'ro_' . $id_menu;
                $id_fullAccess  = 'fa_' . $id_menu;
                $id_noAccess    = 'na_' . $id_menu;

                $chk_readOnly   = $this->custom_checkbox($id_readOnly, $id_menu, 0, $state_readOnly, 'Read Only');
                $chk_fullAccess = $this->custom_checkbox($id_fullAccess, $id_menu, 1, $state_fullAccess, 'Full Access');
                $chk_noAccess   = $this->custom_checkbox($id_noAccess, $id_menu, 9, $state_noAccess, 'No Access');

                $action =  '<div class="g-3 align-center flex-wrap">' . $chk_readOnly . $chk_fullAccess . $chk_noAccess . '</div>';

                $arr[$row] = array(
                    'text'  => $val->name,
                    'id'    => $id_menu
                );

                $icon = $val->icon;

                if (!empty($icon)) {
                    $arr[$row]['icon'] = $icon;
                } else {
                    $arr[$row]['icon'] = "icon ni ni-menu-circled";
                }


                if (!empty($val->parent) || $val->hitung == 0) {
                    $arr[$row]['data']['action'] = $action;
                }

                if (empty($role)) {

                    if ($val->hitung == 0) {
                        $arr[$row]['state'] = array(
                            'opened' => true
                        );
                    }
                } else {

                    if ($val->checked == 1 && $val->hitung == 0) {
                        $arr[$row]['state'] = array(
                            'selected'  => true,
                            'opened'    => true
                        );
                    }
                }

                if ($val->hitung > 0) {
                    $arr[$row]['children'] = $this->viewMenuTemplate($id_menu, $level + 1, $role);
                }
            }
        }

        return $arr;
    }

    public function menuTemplate($parent = '0') {
        return DB::table('menu as a')
            ->select([
                'a.id',
                'a.code',
                'a.parent',
                'a.name',
                'a.icon',
                'a.url',
                'a.status',
                DB::raw('IFNULL(jumlah_menu.jumlah, 0) as hitung')
            ])
            ->leftJoinSub(function ($query) {
                $query->select('parent', DB::raw('COUNT(*) as jumlah'))
                    ->from('menu')
                    ->where('status', 1)
                    ->groupBy('parent');
            }, 'jumlah_menu', 'a.code', '=', 'jumlah_menu.parent')
            ->where('a.parent', $parent)
            ->where('a.status', 1)
            ->get()
            ->toArray();
    }

    public function menuTemplateByRole($parent = '0', $role = '') {
        return DB::table('menu as a')
            ->select([
                'a.id',
                'a.code',
                'a.parent',
                'a.name',
                'a.icon',
                'a.url',
                'a.status as menu_status',
                DB::raw('IFNULL(jumlah_menu.jumlah, 0) as hitung'),
                DB::raw("CASE WHEN c.menu_id IS NOT NULL THEN 1 ELSE 0 END as checked"),
                'c.status'
            ])
            ->leftJoinSub(function ($query) {
                $query->select('parent', DB::raw('COUNT(*) as jumlah'))
                    ->from('menu')
                    ->where('status', 1)
                    ->groupBy('parent');
            }, 'jumlah_menu', 'a.code', '=', 'jumlah_menu.parent')
            ->leftJoin('permissions as c', function ($join) use ($role) {
                $join->on('c.menu_id', '=', 'a.id')
                    ->where('c.role_id', '=', $role);
            })
            ->where('a.parent', $parent)
            ->where('a.status', 1)
            ->get()
            ->toArray();
    }

    public function custom_checkbox($id, $nama = '', $value = '', $state = '', $label_text = '') {

        $nama_attribute = ($nama != '') ? 'name="' . $nama . '"' : '';
        $value_attribute = ($value != '') ? 'value="' . $value . '"' : '';

        $checkbox = '<div class="g">
                        <div class="custom-control custom-control-sm custom-radio">
                            <input type="radio" class="custom-control-input" '
            . $nama_attribute . ' id="' . $id . '" ' . $value_attribute . ' ' . $state . '>
                            <label class="custom-control-label" for="' . $id . '">' . $label_text . '</label>
                        </div>
                    </div>';

        return $checkbox;
    }

    public static function menu() {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        $roleId = $user->role_id;

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
        $headers = DB::table('menu as m')
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

        $menuHtml = '';

        foreach ($headers as $header) {
            $children = DB::table('menu as c')
                ->join('permissions as perm', 'perm.menu_id', '=', 'c.id')
                ->join('roles as r', 'r.id', '=', 'perm.role_id')
                ->where('perm.role_id', $roleId)
                ->where('perm.status', 1)
                ->where('c.parent', $header->code)
                ->where('c.status', 1)
                ->where('r.status', 1)
                ->select('c.id', 'c.parent', 'c.code', 'c.name', 'c.url', 'c.icon')
                ->orderBy('c.id')
                ->get();

            if ($children->isNotEmpty()) {
                $menuHtml .= '<li class="nk-menu-heading">
                    <h6 class="overline-title text-primary-alt">' . $header->name . '</h6>
                </li>';

                foreach ($children as $detail) {
                    $menuHtml .= '<li class="nk-menu-item active current-page">
                        <a href="' . $detail->url . '" class="nk-menu-link ">
                            <span class="nk-menu-icon">
                                <em class="icon ' . $detail->icon . '"></em>
                            </span>
                            <span class="nk-menu-text">' . $detail->name . '</span>
                        </a>
                    </li>';
                }
            }
        }

        return $menuHtml;
    }
}
