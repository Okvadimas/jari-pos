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
        Permission::create([
            'role_id' => 1,
            'menu_id' => 2,
        ]);

        Permission::create([
            'role_id' => 1,
            'menu_id' => 3
        ]);
    }
}
