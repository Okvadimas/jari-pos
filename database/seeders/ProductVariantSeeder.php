<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $variants = [
            // Pelet Ikan Koi Hi-Growth (product_id: 1)
            ['id' => 1, 'product_id' => 1, 'unit_id' => 1, 'name' => '1 Kg', 'sku' => 'PIK-HG-1KG'],
            ['id' => 2, 'product_id' => 1, 'unit_id' => 2, 'name' => '500 gr', 'sku' => 'PIK-HG-500G'],
            ['id' => 3, 'product_id' => 1, 'unit_id' => 2, 'name' => '250 gr', 'sku' => 'PIK-HG-250G'],
            ['id' => 4, 'product_id' => 1, 'unit_id' => 2, 'name' => 'Eceran (100 gr)', 'sku' => 'PIK-HG-ECR'],

            // Pelet Ikan Lele Super (product_id: 2)
            ['id' => 5, 'product_id' => 2, 'unit_id' => 1, 'name' => '5 Kg', 'sku' => 'PIL-SP-5KG'],
            ['id' => 6, 'product_id' => 2, 'unit_id' => 1, 'name' => '1 Kg', 'sku' => 'PIL-SP-1KG'],
            ['id' => 7, 'product_id' => 2, 'unit_id' => 2, 'name' => '500 gr', 'sku' => 'PIL-SP-500G'],

            // Pelet Ikan Hias Tropical (product_id: 3)
            ['id' => 8, 'product_id' => 3, 'unit_id' => 2, 'name' => '100 gr', 'sku' => 'PIH-TR-100G'],
            ['id' => 9, 'product_id' => 3, 'unit_id' => 2, 'name' => '50 gr', 'sku' => 'PIH-TR-50G'],

            // Voer Burung Murai (product_id: 4)
            ['id' => 10, 'product_id' => 4, 'unit_id' => 2, 'name' => '500 gr', 'sku' => 'VBM-500G'],
            ['id' => 11, 'product_id' => 4, 'unit_id' => 2, 'name' => '250 gr', 'sku' => 'VBM-250G'],
            ['id' => 12, 'product_id' => 4, 'unit_id' => 2, 'name' => '100 gr', 'sku' => 'VBM-100G'],

            // Biji Milet Putih (product_id: 5)
            ['id' => 13, 'product_id' => 5, 'unit_id' => 1, 'name' => '1 Kg', 'sku' => 'BMP-1KG'],
            ['id' => 14, 'product_id' => 5, 'unit_id' => 2, 'name' => '500 gr', 'sku' => 'BMP-500G'],
            ['id' => 15, 'product_id' => 5, 'unit_id' => 2, 'name' => '250 gr', 'sku' => 'BMP-250G'],

            // Kroto Segar (product_id: 6)
            ['id' => 16, 'product_id' => 6, 'unit_id' => 2, 'name' => '100 gr', 'sku' => 'KRT-100G'],
            ['id' => 17, 'product_id' => 6, 'unit_id' => 2, 'name' => '50 gr', 'sku' => 'KRT-50G'],

            // Cat Food Premium Adult (product_id: 7)
            ['id' => 18, 'product_id' => 7, 'unit_id' => 1, 'name' => '10 Kg', 'sku' => 'CFP-AD-10KG'],
            ['id' => 19, 'product_id' => 7, 'unit_id' => 1, 'name' => '3 Kg', 'sku' => 'CFP-AD-3KG'],
            ['id' => 20, 'product_id' => 7, 'unit_id' => 1, 'name' => '1 Kg', 'sku' => 'CFP-AD-1KG'],
            ['id' => 21, 'product_id' => 7, 'unit_id' => 2, 'name' => '500 gr', 'sku' => 'CFP-AD-500G'],
            ['id' => 22, 'product_id' => 7, 'unit_id' => 2, 'name' => 'Repack 100 gr', 'sku' => 'CFP-AD-RP100'],

            // Cat Food Kitten (product_id: 8)
            ['id' => 23, 'product_id' => 8, 'unit_id' => 1, 'name' => '3 Kg', 'sku' => 'CFP-KT-3KG'],
            ['id' => 24, 'product_id' => 8, 'unit_id' => 1, 'name' => '1 Kg', 'sku' => 'CFP-KT-1KG'],
            ['id' => 25, 'product_id' => 8, 'unit_id' => 2, 'name' => '500 gr', 'sku' => 'CFP-KT-500G'],

            // Wet Food Tuna (product_id: 9)
            ['id' => 26, 'product_id' => 9, 'unit_id' => 2, 'name' => '85 gr', 'sku' => 'WFT-85G'],

            // Dog Food Adult Large Breed (product_id: 10)
            ['id' => 27, 'product_id' => 10, 'unit_id' => 1, 'name' => '15 Kg', 'sku' => 'DFA-LB-15KG'],
            ['id' => 28, 'product_id' => 10, 'unit_id' => 1, 'name' => '7.5 Kg', 'sku' => 'DFA-LB-7KG'],
            ['id' => 29, 'product_id' => 10, 'unit_id' => 1, 'name' => '3 Kg', 'sku' => 'DFA-LB-3KG'],

            // Dog Food Puppy (product_id: 11)
            ['id' => 30, 'product_id' => 11, 'unit_id' => 1, 'name' => '10 Kg', 'sku' => 'DFP-10KG'],
            ['id' => 31, 'product_id' => 11, 'unit_id' => 1, 'name' => '3 Kg', 'sku' => 'DFP-3KG'],
            ['id' => 32, 'product_id' => 11, 'unit_id' => 1, 'name' => '1 Kg', 'sku' => 'DFP-1KG'],

            // Filter Akuarium Mini (product_id: 12)
            ['id' => 33, 'product_id' => 12, 'unit_id' => 7, 'name' => 'Standard', 'sku' => 'FAM-STD'],

            // Lampu LED Akuarium (product_id: 13)
            ['id' => 34, 'product_id' => 13, 'unit_id' => 16, 'name' => '30 cm', 'sku' => 'LLA-30CM'],
            ['id' => 35, 'product_id' => 13, 'unit_id' => 16, 'name' => '60 cm', 'sku' => 'LLA-60CM'],
            ['id' => 36, 'product_id' => 13, 'unit_id' => 16, 'name' => '90 cm', 'sku' => 'LLA-90CM'],

            // Vitamin Burung Multivit (product_id: 14)
            ['id' => 37, 'product_id' => 14, 'unit_id' => 6, 'name' => '30 ml', 'sku' => 'VBM-30ML'],
            ['id' => 38, 'product_id' => 14, 'unit_id' => 6, 'name' => '15 ml', 'sku' => 'VBM-15ML'],

            // Obat Anti Kutu Kucing (product_id: 15)
            ['id' => 39, 'product_id' => 15, 'unit_id' => 7, 'name' => '1 Pipet', 'sku' => 'OAK-1P'],
            ['id' => 40, 'product_id' => 15, 'unit_id' => 7, 'name' => '3 Pipet', 'sku' => 'OAK-3P'],
        ];

        foreach ($variants as $variant) {
            DB::table('product_variants')->updateOrInsert(
                ['id' => $variant['id']],
                [
                    'product_id' => $variant['product_id'],
                    'unit_id' => $variant['unit_id'],
                    'name' => $variant['name'],
                    'sku' => $variant['sku'],
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
