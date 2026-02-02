<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Company;
use App\Models\ProductVariant;
use App\Models\User;
use Faker\Factory as Faker;

class DummySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get necessary IDs
        $companyIds = Company::pluck('id')->toArray();
        $productVariantIds = ProductVariant::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        if (empty($companyIds) || empty($productVariantIds) || empty($userIds)) {
            $this->command->warn('Companies, Product Variants, or Users missing. Skipping DummySalesSeeder.');
            return;
        }

        $this->command->info('Creating 50 Dummy Sales Orders...');

        for ($i = 0; $i < 50; $i++) {
            DB::transaction(function () use ($faker, $companyIds, $productVariantIds, $userIds) {
                $companyId = $faker->randomElement($companyIds);
                $createdBy = $faker->randomElement($userIds);
                $orderDate = $faker->dateTimeBetween('-1 year', 'now');

                // Create Order
                $salesOrder = SalesOrder::create([
                    'company_id' => $companyId,
                    'order_date' => $orderDate,
                    'total_amount' => 0, // Calculated later
                    'applied_promo_id' => null,
                    'total_discount_manual' => 0,
                    'final_amount' => 0, // Calculated later
                    'created_by' => $createdBy,
                    'updated_by' => $createdBy,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $totalAmount = 0;
                $numberOfItems = $faker->numberBetween(1, 5);

                for ($j = 0; $j < $numberOfItems; $j++) {
                    $variantId = $faker->randomElement($productVariantIds);
                    // Fetch variant price (assuming ProductPrice logic or base price from variant/product relationships, 
                    // but for dummy data, we might just grab a random price or try to get it from DB if easy.
                    // Given the models I saw, ProductVariant might not have price directly or it is in ProductPrice.
                    // To keep it simple and robust without querying too much inside loop, I'll mock the price if I can't easily get it.
                    // Actually, ProductPrice exists. Let's try to be slightly realistic or just random.
                    // Random is safer to avoid complex query logic in seeder if the relation isn't loaded.
                    $unitPrice = $faker->numberBetween(100, 5000) * 1000; // 100k - 5000k (too high?), maybe 10k to 500k
                    $unitPrice = $faker->numberBetween(10, 500) * 1000; // 10.000 - 500.000

                    $quantity = $faker->numberBetween(1, 10);
                    $subtotal = $unitPrice * $quantity;

                    SalesOrderDetail::create([
                        'sales_order_id' => $salesOrder->id,
                        'product_variant_id' => $variantId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_auto_amount' => 0,
                        'subtotal' => $subtotal,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);

                    $totalAmount += $subtotal;
                }

                // Update totals
                $salesOrder->update([
                    'total_amount' => $totalAmount,
                    'final_amount' => $totalAmount, // Assuming no discount for simplicity in this random batch
                ]);
            });
        }
    }
}
