<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Satuan Berat
            [
                'code'       => 'KG',
                'name'       => 'Kilogram',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'G',
                'name'       => 'Gram',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'MG',
                'name'       => 'Miligram',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'ONS',
                'name'       => 'Ons',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Volume
            [
                'code'       => 'L',
                'name'       => 'Liter',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'ML',
                'name'       => 'Mililiter',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Kuantitas
            [
                'code'       => 'PCS',
                'name'       => 'Pieces',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'UNIT',
                'name'       => 'Unit',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'SET',
                'name'       => 'Set',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'PACK',
                'name'       => 'Pack',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'BOX',
                'name'       => 'Box',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'DUS',
                'name'       => 'Dus',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'LUSIN',
                'name'       => 'Lusin',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'KODI',
                'name'       => 'Kodi',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Panjang
            [
                'code'       => 'M',
                'name'       => 'Meter',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'CM',
                'name'       => 'Centimeter',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Lainnya
            [
                'code'       => 'BTL',
                'name'       => 'Botol',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'SACHET',
                'name'       => 'Sachet',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'KALENG',
                'name'       => 'Kaleng',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'CUP',
                'name'       => 'Cup',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'code'       => 'PORSI',
                'name'       => 'Porsi',
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
