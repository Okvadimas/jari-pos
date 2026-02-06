<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some references if available
        $categoryId = Category::inRandomOrder()->value('id');
        $productId = Product::inRandomOrder()->value('id');

        $promotions = [
            [
                'company_id' => 1,
                'name' => 'DISKON AWAL TAHUN',
                'type' => 'fixed',
                'discount_value' => 10000,
                'min_order_amount' => 50000,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'category_id' => null,
                'product_id' => null,
                'priority' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'VOUCHER SPESIAL MEMBER',
                'type' => 'fixed',
                'discount_value' => 25000,
                'min_order_amount' => 100000,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addMonths(1),
                'category_id' => null,
                'product_id' => null,
                'priority' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'FLASH SALE KATEGORI',
                'type' => 'fixed',
                'discount_value' => 5000,
                'min_order_amount' => 20000,
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(3),
                'category_id' => $categoryId, // Specific to a category
                'product_id' => null,
                'priority' => 3,
            ],
            [
                'company_id' => 1,
                'name' => 'PROMO PRODUK PILIHAN',
                'type' => 'fixed',
                'discount_value' => 15000,
                'min_order_amount' => 0, // No min order
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'category_id' => null,
                'product_id' => $productId, // Specific to a product
                'priority' => 4,
            ],
            [
                'company_id' => 1,
                'name' => 'EXPIRED VOUCHER',
                'type' => 'fixed',
                'discount_value' => 50000,
                'min_order_amount' => 100000,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->subMonths(1),
                'category_id' => null,
                'product_id' => null,
                'priority' => 5,
            ]
        ];

        foreach ($promotions as $promo) {
            Promotion::firstOrCreate(
                ['name' => $promo['name']],
                $promo
            );
        }
    }
}
