<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        Menu::truncate();

        $menus = [
            // Parent Menus (parent = '0')
            [
                'kode'      => 'MJ',
                'parent'    => '0',
                'nama'      => 'MANAJEMEN',
                'icon'      => 'ni ni-article',
                'url'       => null,
                'status'    => 'active',
                'insert_at' => now(),
                'insert_by' => 'system',
            ],

            // Child Menus 
            [
                'kode'      => 'MJ-01',
                'parent'    => 'MJ',
                'nama'      => 'Manajemen User',
                'icon'      => 'ni ni-user-fill',
                'url'       => '/management/user',
                'status'    => 'active',
                'insert_at' => now(),
                'insert_by' => 'system',
            ],
            [
                'kode'      => 'MJ-02',
                'parent'    => 'MJ',
                'nama'      => 'Manajemen Akses',
                'icon'      => 'ni ni-account-setting-fill',
                'url'       => '/management/akses',
                'status'    => 'active',
                'insert_at' => now(),
                'insert_by' => 'system',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
