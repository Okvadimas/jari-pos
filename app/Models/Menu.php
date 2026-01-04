<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    public $timestamps = false;
    protected $table = 'menu';
    protected $fillable = [
        'id',
        'kode',
        'parent',
        'nama',
        'icon',
        'url',
        'status',
        'insert_at',
        'insert_by',
        'update_at',
        'update_by'
    ];

    public function dataTableMenu() {
        return self::select('id', 'kode', 'parent', 'nama', 'icon', 'url', 'status')->where('status', 'active');
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
                $id_menu = $val->kode;

                if (empty($role)) {
                    // Semua action, default-nya unchecked saat akan membuat role baru.
                    $state_readOnly     = '';
                    $state_fullAccess   = '';
                    $state_noAccess     = '';
                } else {
                    $state_readOnly     = ($val->flag_access == 0) ? 'checked' : '';
                    $state_fullAccess   = ($val->flag_access == 1) ? 'checked' : '';
                    $state_noAccess     = ($val->flag_access == 9) ? 'checked' : '';
                }

                $id_readOnly    = 'ro_' . $id_menu;
                $id_fullAccess  = 'fa_' . $id_menu;
                $id_noAccess    = 'na_' . $id_menu;

                $chk_readOnly   = $this->custom_checkbox($id_readOnly, $id_menu, 0, $state_readOnly, 'Read Only');
                $chk_fullAccess = $this->custom_checkbox($id_fullAccess, $id_menu, 1, $state_fullAccess, 'Full Access');
                $chk_noAccess   = $this->custom_checkbox($id_noAccess, $id_menu, 9, $state_noAccess, 'No Access');

                $action =  '<div class="g-3 align-center flex-wrap">' . $chk_readOnly . $chk_fullAccess . $chk_noAccess . '</div>';

                $arr[$row] = array(
                    'text'  => $val->nama,
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
        $sql = "SELECT a.*, IFNULL(jumlah_menu.jumlah, 0) AS hitung
                FROM menu a
                    LEFT JOIN (
                        SELECT parent, COUNT(*) AS jumlah
                        FROM menu
                        WHERE status = 'active'
                        GROUP BY parent
                    ) AS jumlah_menu ON a.kode = jumlah_menu.parent
                WHERE a.parent = '$parent' AND a.status = 'active'
                ";

        $data = DB::select($sql);
        return $data;
    }

    public function menuTemplateByRole($parent = '0', $role = '') {
        $sql = "SELECT a.*, IFNULL(jumlah_menu.jumlah, 0) AS hitung,
                    CASE WHEN (c.kode_menu <> '') 
                        THEN TRUE 
                        ELSE FALSE 
                    END AS checked,
                    c.flag_access
                FROM menu a
                LEFT JOIN (
                    SELECT parent, COUNT(*) AS jumlah
                    FROM menu
                    WHERE status = 'active'
                    GROUP BY parent
                ) AS jumlah_menu ON a.kode = jumlah_menu.parent
                LEFT JOIN (
                    SELECT kode_menu, flag_access
                    FROM akses_role 
                    WHERE role = '$role'
                ) AS c ON c.kode_menu = a.kode
                WHERE a.parent = '$parent' AND a.status = 'active'";

        $data = DB::select($sql);
        return $data;
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
        $header_menu = DB::select("SELECT m.id, m.parent, m.kode, m.nama, m.icon, m.url
                        from menu m 
                        join (
                            select m.parent as kode
                            from users u 
                            join `role` r on u.role = r.slug
                            join akses ar on r.slug = ar.role 
                            join menu m on ar.kode_menu = m.kode 
                            where ar.flag_access != 9 and u.role = '$user->role' and m.status = 'active' and r.status = 'active'
                            group by m.parent 
                        ) sq on m.kode = sq.kode
                        where m.status = 'active'
                        union all
                        SELECT m.id, m.parent, m.kode, m.nama, m.icon, m.url
                        from menu m 
                        join (
                            select m.kode
                            from users u 
                            join `role` r on u.role = r.slug
                            join akses ar on r.slug = ar.role
                            join menu m on ar.kode_menu = m.kode 
                            where ar.flag_access != 9 and u.role = '$user->role' and m.status = 'active' and m.parent = '0' and r.status = 'active'
                        ) sq on m.kode = sq.kode
                        where m.status = 'active'
                        order by id");

        $menu = '';
        foreach($header_menu as $row) {

            $detail_menu = DB::select("SELECT m.parent, m.kode, m.nama, m.url, m.icon
                                from users u 
                                join `role` r on u.role = r.slug
                                join akses ar on r.slug = ar.role 
                                join menu m on ar.kode_menu = m.kode 
                                where ar.flag_access != 9 and u.role = '$user->role' and m.parent = '$row->kode' and m.status = 'active' and r.status = 'active'
                                order by m.id");

            if(!empty($detail_menu)) {

                $menu .= '<li class="nk-menu-heading">
                    <h6 class="overline-title text-primary-alt">'.$row->nama.'</h6>
                </li>';

                foreach ($detail_menu as $detail) {
    
                    $menu .= '<li class="nk-menu-item active current-page">
                        <a href="'.$detail->url.'" class="nk-menu-link ">
                            <span class="nk-menu-icon">
                                <em class="icon '.$detail->icon.'"></em>
                            </span>
                            <span class="nk-menu-text">'.$detail->nama.'</span>
                        </a>
                    </li>';
                }

            }
        }

        return $menu;
    }
}
