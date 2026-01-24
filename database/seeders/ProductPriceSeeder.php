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
            ['product_variant_id' => 1, 'purchase_price' => 68000, 'sell_price' => 85000],   // 1 Kg
            ['product_variant_id' => 2, 'purchase_price' => 36000, 'sell_price' => 45000],   // 500 gr
            ['product_variant_id' => 3, 'purchase_price' => 20000, 'sell_price' => 25000],   // 250 gr
            ['product_variant_id' => 4, 'purchase_price' => 9600, 'sell_price' => 12000],    // Eceran

            // Pelet Ikan Lele Super
            ['product_variant_id' => 5, 'purchase_price' => 76000, 'sell_price' => 95000],   // 5 Kg
            ['product_variant_id' => 6, 'purchase_price' => 17600, 'sell_price' => 22000],   // 1 Kg
            ['product_variant_id' => 7, 'purchase_price' => 9600, 'sell_price' => 12000],    // 500 gr

            // Pelet Ikan Hias Tropical
            ['product_variant_id' => 8, 'purchase_price' => 28000, 'sell_price' => 35000],   // 100 gr
            ['product_variant_id' => 9, 'purchase_price' => 16000, 'sell_price' => 20000],   // 50 gr

            // Voer Burung Murai
            ['product_variant_id' => 10, 'purchase_price' => 60000, 'sell_price' => 75000],  // 500 gr
            ['product_variant_id' => 11, 'purchase_price' => 32000, 'sell_price' => 40000],  // 250 gr
            ['product_variant_id' => 12, 'purchase_price' => 14400, 'sell_price' => 18000],  // 100 gr

            // Biji Milet Putih
            ['product_variant_id' => 13, 'purchase_price' => 20000, 'sell_price' => 25000],  // 1 Kg
            ['product_variant_id' => 14, 'purchase_price' => 12000, 'sell_price' => 15000],  // 500 gr
            ['product_variant_id' => 15, 'purchase_price' => 6400, 'sell_price' => 8000],    // 250 gr

            // Kroto Segar
            ['product_variant_id' => 16, 'purchase_price' => 44000, 'sell_price' => 55000],  // 100 gr
            ['product_variant_id' => 17, 'purchase_price' => 24000, 'sell_price' => 30000],  // 50 gr

            // Cat Food Premium Adult
            ['product_variant_id' => 18, 'purchase_price' => 520000, 'sell_price' => 650000], // 10 Kg
            ['product_variant_id' => 19, 'purchase_price' => 168000, 'sell_price' => 210000], // 3 Kg
            ['product_variant_id' => 20, 'purchase_price' => 62400, 'sell_price' => 78000],   // 1 Kg
            ['product_variant_id' => 21, 'purchase_price' => 33600, 'sell_price' => 42000],   // 500 gr
            ['product_variant_id' => 22, 'purchase_price' => 8000, 'sell_price' => 10000],    // Repack 100 gr

            // Cat Food Kitten
            ['product_variant_id' => 23, 'purchase_price' => 188000, 'sell_price' => 235000], // 3 Kg
            ['product_variant_id' => 24, 'purchase_price' => 68000, 'sell_price' => 85000],   // 1 Kg
            ['product_variant_id' => 25, 'purchase_price' => 37600, 'sell_price' => 47000],   // 500 gr

            // Wet Food Tuna
            ['product_variant_id' => 26, 'purchase_price' => 9600, 'sell_price' => 12000],    // 85 gr

            // Dog Food Adult Large Breed
            ['product_variant_id' => 27, 'purchase_price' => 680000, 'sell_price' => 850000], // 15 Kg
            ['product_variant_id' => 28, 'purchase_price' => 360000, 'sell_price' => 450000], // 7.5 Kg
            ['product_variant_id' => 29, 'purchase_price' => 156000, 'sell_price' => 195000], // 3 Kg

            // Dog Food Puppy
            ['product_variant_id' => 30, 'purchase_price' => 464000, 'sell_price' => 580000], // 10 Kg
            ['product_variant_id' => 31, 'purchase_price' => 156000, 'sell_price' => 195000], // 3 Kg
            ['product_variant_id' => 32, 'purchase_price' => 57600, 'sell_price' => 72000],   // 1 Kg

            // Filter Akuarium Mini
            ['product_variant_id' => 33, 'purchase_price' => 68000, 'sell_price' => 85000],   // Standard

            // Lampu LED Akuarium
            ['product_variant_id' => 34, 'purchase_price' => 60000, 'sell_price' => 75000],   // 30 cm
            ['product_variant_id' => 35, 'purchase_price' => 100000, 'sell_price' => 125000], // 60 cm
            ['product_variant_id' => 36, 'purchase_price' => 140000, 'sell_price' => 175000], // 90 cm

            // Vitamin Burung Multivit
            ['product_variant_id' => 37, 'purchase_price' => 28000, 'sell_price' => 35000],   // 30 ml
            ['product_variant_id' => 38, 'purchase_price' => 16000, 'sell_price' => 20000],   // 15 ml

            // Obat Anti Kutu Kucing
            ['product_variant_id' => 39, 'purchase_price' => 36000, 'sell_price' => 45000],   // 1 Pipet
            ['product_variant_id' => 40, 'purchase_price' => 96000, 'sell_price' => 120000],  // 3 Pipet
        ];

        foreach ($prices as $price) {
            DB::table('product_prices')->insert([
                'product_variant_id' => $price['product_variant_id'],
                'purchase_price' => $price['purchase_price'],
                'sell_price' => $price['sell_price'],
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
