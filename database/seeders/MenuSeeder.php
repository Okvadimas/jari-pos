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
        $menus = [
            // Parent Menus (parent = '0')
            [
                'code'      => 'MJ',
                'parent'    => '0',
                'name'      => 'MANAJEMEN',
                'icon'      => 'ni ni-article',
                'url'       => null,
            ],

            // Child Menus 
            [
                'code'      => 'MJ-01',
                'parent'    => 'MJ',
                'name'      => 'Manajemen User',
                'icon'      => 'ni ni-user-fill',
                'url'       => '/management/user',
            ],
            [
                'code'      => 'MJ-02',
                'parent'    => 'MJ',
                'name'      => 'Manajemen Akses',
                'icon'      => 'ni ni-account-setting-fill',
                'url'       => '/management/akses',
            ],
            [
                'code'      => 'MJ-03',
                'parent'    => 'MJ',
                'name'      => 'Manajemen Perusahaan',
                'icon'      => 'ni ni-building-fill',
                'url'       => '/management/company',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['code' => $menu['code']],
                $menu
            );
        }
    }
}
