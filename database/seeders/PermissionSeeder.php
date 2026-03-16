<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Menu;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleMenus = [
            1 => Menu::whereNotNull('url')->pluck('id')->toArray(), // Super Admin
            2 => [5, 7, 8, 9, 10, 11, 13, 14],                      // Kelingking
            3 => [5, 7, 8, 9, 10, 11, 13, 14, 16, 18],              // Jempol
        ];

        foreach ($roleMenus as $roleId => $menuIds) {
            foreach ($menuIds as $menuId) {
                Permission::updateOrCreate([
                    'role_id'   => $roleId,
                    'menu_id'   => $menuId,
                ], [
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
            }
        }
    }
}
