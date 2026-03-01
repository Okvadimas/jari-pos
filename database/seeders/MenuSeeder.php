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
            [
                'code'      => 'IN-03',
                'parent'    => 'IN',
                'name'      => 'Produk',
                'icon'      => 'ni ni-list',
                'url'       => '/inventory/product',
            ],
            [
                'code'      => 'IN-04',
                'parent'    => 'IN',
                'name'      => 'Produk Varian',
                'icon'      => 'ni ni-list-thumb-fill',
                'url'       => '/inventory/product-variant',
            ],
            [
                'code'      => 'IN-05',
                'parent'    => 'IN',
                'name'      => 'Stock Opname',
                'icon'      => 'ni ni-clip-board-check',
                'url'       => '/inventory/stock-opname',
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
                'url'       => '/transaction/purchasing',
            ],
            [
                'code'      => 'TR-02',
                'parent'    => 'TR',
                'name'      => 'Penjualan',
                'icon'      => 'ni ni-cc-alt2-fill',
                'url'       => '/transaction/sales',
            ],

            // Laporan
            [
                'code'      => 'LP',
                'parent'    => '0',
                'name'      => 'LAPORAN',
                'icon'      => 'ni ni-article',
                'url'       => null,
            ],

            // Child Laporan
            [
                'code'      => 'LP-01',
                'parent'    => 'LP',
                'name'      => 'Rekomendasi Stok',
                'icon'      => 'ni ni-cc-alt2-fill',
                'url'       => '/report/stock-recommendation',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['code' => $menu['code']],
                array_merge($menu, [
                    'created_by' => 1,
                    'updated_by' => 1,
                ])
            );
        }
    }
}
