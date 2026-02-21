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
            ['id' => 1, 'product_id' => 1, 'name' => '1 Kg', 'sku' => 'PIK-HG-1KG', 'current_stock' => 120],
            ['id' => 2, 'product_id' => 1, 'name' => '500 gr', 'sku' => 'PIK-HG-500G', 'current_stock' => 50],
            ['id' => 3, 'product_id' => 1, 'name' => '250 gr', 'sku' => 'PIK-HG-250G', 'current_stock' => 30],
            ['id' => 4, 'product_id' => 1, 'name' => 'Eceran (100 gr)', 'sku' => 'PIK-HG-ECR', 'current_stock' => 50],

            // Pelet Ikan Lele Super (product_id: 2)
            ['id' => 5, 'product_id' => 2, 'name' => '5 Kg', 'sku' => 'PIL-SP-5KG', 'current_stock' => 200],
            ['id' => 6, 'product_id' => 2, 'name' => '1 Kg', 'sku' => 'PIL-SP-1KG', 'current_stock' => 80],
            ['id' => 7, 'product_id' => 2, 'name' => '500 gr', 'sku' => 'PIL-SP-500G', 'current_stock' => 60],

            // Pelet Ikan Hias Tropical (product_id: 3)
            ['id' => 8, 'product_id' => 3, 'name' => '100 gr', 'sku' => 'PIH-TR-100G', 'current_stock' => 25],
            ['id' => 9, 'product_id' => 3, 'name' => '50 gr', 'sku' => 'PIH-TR-50G', 'current_stock' => 40],

            // Voer Burung Murai (product_id: 4)
            ['id' => 10, 'product_id' => 4, 'name' => '500 gr', 'sku' => 'VBM-500G', 'current_stock' => 40],
            ['id' => 11, 'product_id' => 4, 'name' => '250 gr', 'sku' => 'VBM-250G', 'current_stock' => 20],
            ['id' => 12, 'product_id' => 4, 'name' => '100 gr', 'sku' => 'VBM-100G', 'current_stock' => 35],

            // Biji Milet Putih (product_id: 5)
            ['id' => 13, 'product_id' => 5, 'name' => '1 Kg', 'sku' => 'BMP-1KG', 'current_stock' => 55],
            ['id' => 14, 'product_id' => 5, 'name' => '500 gr', 'sku' => 'BMP-500G', 'current_stock' => 45],
            ['id' => 15, 'product_id' => 5, 'name' => '250 gr', 'sku' => 'BMP-250G', 'current_stock' => 18],

            // Kroto Segar (product_id: 6)
            ['id' => 16, 'product_id' => 6, 'name' => '100 gr', 'sku' => 'KRT-100G', 'current_stock' => 15],
            ['id' => 17, 'product_id' => 6, 'name' => '50 gr', 'sku' => 'KRT-50G', 'current_stock' => 25],

            // Cat Food Premium Adult (product_id: 7)
            ['id' => 18, 'product_id' => 7, 'name' => '10 Kg', 'sku' => 'CFP-AD-10KG', 'current_stock' => 60],
            ['id' => 19, 'product_id' => 7, 'name' => '3 Kg', 'sku' => 'CFP-AD-3KG', 'current_stock' => 90],
            ['id' => 20, 'product_id' => 7, 'name' => '1 Kg', 'sku' => 'CFP-AD-1KG', 'current_stock' => 150],
            ['id' => 21, 'product_id' => 7, 'name' => '500 gr', 'sku' => 'CFP-AD-500G', 'current_stock' => 65],
            ['id' => 22, 'product_id' => 7, 'name' => 'Repack 100 gr', 'sku' => 'CFP-AD-RP100', 'current_stock' => 20],

            // Cat Food Kitten (product_id: 8)
            ['id' => 23, 'product_id' => 8, 'name' => '3 Kg', 'sku' => 'CFP-KT-3KG', 'current_stock' => 70],
            ['id' => 24, 'product_id' => 8, 'name' => '1 Kg', 'sku' => 'CFP-KT-1KG', 'current_stock' => 50],
            ['id' => 25, 'product_id' => 8, 'name' => '500 gr', 'sku' => 'CFP-KT-500G', 'current_stock' => 40],

            // Wet Food Tuna (product_id: 9)
            ['id' => 26, 'product_id' => 9, 'name' => '85 gr', 'sku' => 'WFT-85G', 'current_stock' => 300],

            // Dog Food Adult Large Breed (product_id: 10)
            ['id' => 27, 'product_id' => 10, 'name' => '15 Kg', 'sku' => 'DFA-LB-15KG', 'current_stock' => 10],
            ['id' => 28, 'product_id' => 10, 'name' => '7.5 Kg', 'sku' => 'DFA-LB-7KG', 'current_stock' => 15],
            ['id' => 29, 'product_id' => 10, 'name' => '3 Kg', 'sku' => 'DFA-LB-3KG', 'current_stock' => 12],

            // Dog Food Puppy (product_id: 11)
            ['id' => 30, 'product_id' => 11, 'name' => '10 Kg', 'sku' => 'DFP-10KG', 'current_stock' => 30],
            ['id' => 31, 'product_id' => 11, 'name' => '3 Kg', 'sku' => 'DFP-3KG', 'current_stock' => 35],
            ['id' => 32, 'product_id' => 11, 'name' => '1 Kg', 'sku' => 'DFP-1KG', 'current_stock' => 15],

            // Filter Akuarium Mini (product_id: 12)
            ['id' => 33, 'product_id' => 12, 'name' => 'Standard', 'sku' => 'FAM-STD', 'current_stock' => 8],

            // Lampu LED Akuarium (product_id: 13)
            ['id' => 34, 'product_id' => 13, 'name' => '30 cm', 'sku' => 'LLA-30CM', 'current_stock' => 12],
            ['id' => 35, 'product_id' => 13, 'name' => '60 cm', 'sku' => 'LLA-60CM', 'current_stock' => 6],
            ['id' => 36, 'product_id' => 13, 'name' => '90 cm', 'sku' => 'LLA-90CM', 'current_stock' => 4],

            // Vitamin Burung Multivit (product_id: 14)
            ['id' => 37, 'product_id' => 14, 'name' => '30 ml', 'sku' => 'VBM-30ML', 'current_stock' => 20],
            ['id' => 38, 'product_id' => 14, 'name' => '15 ml', 'sku' => 'VBM-15ML', 'current_stock' => 15],

            // Obat Anti Kutu Kucing (product_id: 15)
            ['id' => 39, 'product_id' => 15, 'name' => '1 Pipet', 'sku' => 'OAK-1P', 'current_stock' => 18],
            ['id' => 40, 'product_id' => 15, 'name' => '3 Pipet', 'sku' => 'OAK-3P', 'current_stock' => 10],
        ];

        foreach ($variants as $variant) {
            DB::table('product_variants')->updateOrInsert(
                ['id' => $variant['id']],
                [
                    'product_id' => $variant['product_id'],
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
