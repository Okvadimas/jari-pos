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
        $menus = Menu::whereNotNull('url')->get();

        foreach ($menus as $menu) {
            Permission::updateOrCreate([
                'role_id'   => 1,
                'menu_id'   => $menu->id,
            ], [
                'status'    => 1,
            ]);
        }
    }
}
