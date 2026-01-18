<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::updateOrCreate([
            'role_id'   => 1,
            'menu_id'   => 2,
            'status'    => 1,
        ]);

        Permission::updateOrCreate([
            'role_id'   => 1,
            'menu_id'   => 3,
            'status'    => 1,
        ]);

        Permission::updateOrCreate([
            'role_id'   => 1,
            'menu_id'   => 4,
            'status'    => 1,
        ]);

        Permission::updateOrCreate([
            'role_id'   => 1,
            'menu_id'   => 5,
            'status'    => 1,
        ]);

        Permission::updateOrCreate([
            'role_id'   => 1,
            'menu_id'   => 7,
            'status'    => 1,
        ]);

        Permission::updateOrCreate([
            'role_id'   => 1,
            'menu_id'   => 8,
            'status'    => 1,
        ]);
    }
}
