<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\PackagePrice;
use App\Models\PackageDetail;
use App\Models\Role;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan role id untuk Jempol dan Kelingking jika ada
        $roleKelingking = Role::where('slug', 'kelingking')->first(); // asumsi slug role
        $roleJempol = Role::where('slug', 'jempol')->first(); 
        // Jika tidak ketemu pakai ID default 2 dan 3
        $roleKelingkingId = $roleKelingking ? $roleKelingking->id : 2;
        $roleJempolId = $roleJempol ? $roleJempol->id : 3;

        // 1. Paket Kelingking (Gratis)
        $paketKelingking = Package::create([
            'name' => 'Kelingking',
            'description' => 'Paket dasar gratis yang cocok untuk mulai usaha kecil.',
            'role_id' => $roleKelingkingId,
            'is_active' => true,
        ]);

        $kelingkingBenefits = [
            'Dashboard interaktif',
            'Produk teratas',
            'Pencatatan transaksi',
            'Cetak struk Bluetooth',
        ];

        foreach ($kelingkingBenefits as $benefit) {
            PackageDetail::create([
                'package_id' => $paketKelingking->id,
                'benefit_description' => $benefit,
            ]);
        }

        // 2. Paket Jempol (Berbayar)
        $paketJempol = Package::create([
            'name' => 'Jempol',
            'description' => 'Paket pro lengkap untuk mengelola seluruh aspek bisnis Anda.',
            'role_id' => $roleJempolId,
            'is_active' => true,
        ]);

        $jempolPrices = [
            1  => 85000,
            3  => 225000,
            6  => 399000,
            12 => 699000,
        ];

        foreach ($jempolPrices as $months => $price) {
            PackagePrice::create([
                'package_id' => $paketJempol->id,
                'duration_months' => $months,
                'price' => $price,
                'is_active' => true,
            ]);
        }

        $jempolBenefits = [
            'Semua fitur Kelingking',
            'Kelola pelanggan',
            'Manajemen Bahan Baku & Stok',
            'Sistem Keuangan & Laporan Jurnal',
            'Ekspor Laporan Excel Format',
        ];

        foreach ($jempolBenefits as $benefit) {
            PackageDetail::create([
                'package_id' => $paketJempol->id,
                'benefit_description' => $benefit,
            ]);
        }
    }
}
