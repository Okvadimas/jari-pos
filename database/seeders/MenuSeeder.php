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
            // Management
            [
                'code'      => 'MJ',
                'parent'    => '0',
                'name'      => 'MANAJEMEN',
                'icon'      => 'ni ni-article',
                'url'       => null,
            ],

            // Child Management 
            [
                'code'      => 'MJ-01',
                'parent'    => 'MJ',
                'name'      => 'User',
                'icon'      => 'ni ni-user-fill',
                'url'       => '/management/user',
            ],
            [
                'code'      => 'MJ-02',
                'parent'    => 'MJ',
                'name'      => 'Akses',
                'icon'      => 'ni ni-account-setting-fill',
                'url'       => '/management/akses',
            ],
            [
                'code'      => 'MJ-03',
                'parent'    => 'MJ',
                'name'      => 'Perusahaan',
                'icon'      => 'ni ni-building-fill',
                'url'       => '/management/company',
            ],
            [
                'code'      => 'MJ-04',
                'parent'    => 'MJ',
                'name'      => 'Metode Pembayaran',
                'icon'      => 'ni ni-grid-fill',
                'url'       => '/management/payment',
            ],

            // Inventory
            [
                'code'      => 'IN',
                'parent'    => '0',
                'name'      => 'INVENTORI',
                'icon'      => 'ni ni-article',
                'url'       => null,
            ],

            // Child Inventory
            [
                'code'      => 'IN-01',
                'parent'    => 'IN',
                'name'      => 'Satuan',
                'icon'      => 'ni ni-grid-fill',
                'url'       => '/inventory/unit',
            ],
            [
                'code'      => 'IN-02',
                'parent'    => 'IN',
                'name'      => 'Kategori',
                'icon'      => 'ni ni-tag',
                'url'       => '/inventory/category',
            ],

            // Transaksi
            [
                'code'      => 'TR',
                'parent'    => '0',
                'name'      => 'TRANSAKSI',
                'icon'      => 'ni ni-article',
                'url'       => null,
            ],

            // Child Transaksi
            [
                'code'      => 'TR-01',
                'parent'    => 'TR',
                'name'      => 'Pembelian',
                'icon'      => 'ni ni-cart-fill',
                'url'       => '/transaction/purchase',
            ],
            [
                'code'      => 'TR-02',
                'parent'    => 'TR',
                'name'      => 'Penjualan',
                'icon'      => 'ni ni-cc-alt2-fill',
                'url'       => '/transaction/sales',
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
