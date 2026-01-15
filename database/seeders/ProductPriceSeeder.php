<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prices = [
            // Pelet Ikan Koi Hi-Growth
            ['product_variant_id' => 1, 'price' => 85000],   // 1 Kg
            ['product_variant_id' => 2, 'price' => 45000],   // 500 gr
            ['product_variant_id' => 3, 'price' => 25000],   // 250 gr
            ['product_variant_id' => 4, 'price' => 12000],   // Eceran

            // Pelet Ikan Lele Super
            ['product_variant_id' => 5, 'price' => 95000],   // 5 Kg
            ['product_variant_id' => 6, 'price' => 22000],   // 1 Kg
            ['product_variant_id' => 7, 'price' => 12000],   // 500 gr

            // Pelet Ikan Hias Tropical
            ['product_variant_id' => 8, 'price' => 35000],   // 100 gr
            ['product_variant_id' => 9, 'price' => 20000],   // 50 gr

            // Voer Burung Murai
            ['product_variant_id' => 10, 'price' => 75000],  // 500 gr
            ['product_variant_id' => 11, 'price' => 40000],  // 250 gr
            ['product_variant_id' => 12, 'price' => 18000],  // 100 gr

            // Biji Milet Putih
            ['product_variant_id' => 13, 'price' => 25000],  // 1 Kg
            ['product_variant_id' => 14, 'price' => 15000],  // 500 gr
            ['product_variant_id' => 15, 'price' => 8000],   // 250 gr

            // Kroto Segar
            ['product_variant_id' => 16, 'price' => 55000],  // 100 gr
            ['product_variant_id' => 17, 'price' => 30000],  // 50 gr

            // Cat Food Premium Adult
            ['product_variant_id' => 18, 'price' => 650000], // 10 Kg
            ['product_variant_id' => 19, 'price' => 210000], // 3 Kg
            ['product_variant_id' => 20, 'price' => 78000],  // 1 Kg
            ['product_variant_id' => 21, 'price' => 42000],  // 500 gr
            ['product_variant_id' => 22, 'price' => 10000],  // Repack 100 gr

            // Cat Food Kitten
            ['product_variant_id' => 23, 'price' => 235000], // 3 Kg
            ['product_variant_id' => 24, 'price' => 85000],  // 1 Kg
            ['product_variant_id' => 25, 'price' => 47000],  // 500 gr

            // Wet Food Tuna
            ['product_variant_id' => 26, 'price' => 12000],  // 85 gr

            // Dog Food Adult Large Breed
            ['product_variant_id' => 27, 'price' => 850000], // 15 Kg
            ['product_variant_id' => 28, 'price' => 450000], // 7.5 Kg
            ['product_variant_id' => 29, 'price' => 195000], // 3 Kg

            // Dog Food Puppy
            ['product_variant_id' => 30, 'price' => 580000], // 10 Kg
            ['product_variant_id' => 31, 'price' => 195000], // 3 Kg
            ['product_variant_id' => 32, 'price' => 72000],  // 1 Kg

            // Filter Akuarium Mini
            ['product_variant_id' => 33, 'price' => 85000],  // Standard

            // Lampu LED Akuarium
            ['product_variant_id' => 34, 'price' => 75000],  // 30 cm
            ['product_variant_id' => 35, 'price' => 125000], // 60 cm
            ['product_variant_id' => 36, 'price' => 175000], // 90 cm

            // Vitamin Burung Multivit
            ['product_variant_id' => 37, 'price' => 35000],  // 30 ml
            ['product_variant_id' => 38, 'price' => 20000],  // 15 ml

            // Obat Anti Kutu Kucing
            ['product_variant_id' => 39, 'price' => 45000],  // 1 Pipet
            ['product_variant_id' => 40, 'price' => 120000], // 3 Pipet
        ];

        foreach ($prices as $price) {
            DB::table('product_prices')->insert([
                'product_variant_id' => $price['product_variant_id'],
                'price' => $price['price'],
                'is_active' => true,
                'created_at' => now(),
            ]);
        }
    }
}
