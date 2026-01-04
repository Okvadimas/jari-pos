<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class AksesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        DB::table('akses')->truncate();

        // Get role IDs
        $superAdminId = Role::where('slug', 'super-admin')->first()->slug;

        // Menu codes
        $allMenus = [
            ['kode' => 'MJ-01', 'nama' => 'Manajemen User'],
            ['kode' => 'MJ-02', 'nama' => 'Manajemen Akses'],
        ];

        $aksesData = [];

        // Super Admin - Full Access to all menus
        foreach ($allMenus as $menu) {
            $aksesData[] = [
                'role'          => $superAdminId,
                'kode_menu'     => $menu['kode'],
                'nama_menu'     => $menu['nama'],
                'flag_access'   => 1,
                'insert_at'     => now(),
                'insert_by'     => 'system',
            ];
        }

        // Insert all access data
        DB::table('akses')->insert($aksesData);
    }
}
