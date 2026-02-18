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
                'company_id' => 1,
                'code'       => 'KG',
                'name'       => 'Kilogram',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'G',
                'name'       => 'Gram',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'MG',
                'name'       => 'Miligram',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'ONS',
                'name'       => 'Ons',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Volume
            [
                'company_id' => 1,
                'code'       => 'L',
                'name'       => 'Liter',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'ML',
                'name'       => 'Mililiter',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Kuantitas
            [
                'company_id' => 1,
                'code'       => 'PCS',
                'name'       => 'Pieces',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'UNIT',
                'name'       => 'Unit',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'SET',
                'name'       => 'Set',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'PACK',
                'name'       => 'Pack',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'BOX',
                'name'       => 'Box',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'DUS',
                'name'       => 'Dus',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'LUSIN',
                'name'       => 'Lusin',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'KODI',
                'name'       => 'Kodi',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Panjang
            [
                'company_id' => 1,
                'code'       => 'M',
                'name'       => 'Meter',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'CM',
                'name'       => 'Centimeter',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Satuan Lainnya
            [
                'company_id' => 1,
                'code'       => 'BTL',
                'name'       => 'Botol',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'SACHET',
                'name'       => 'Sachet',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'KALENG',
                'name'       => 'Kaleng',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'CUP',
                'name'       => 'Cup',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'company_id' => 1,
                'code'       => 'PORSI',
                'name'       => 'Porsi',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
