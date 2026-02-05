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

class SalesSeeder extends Seeder
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

        $this->command->info('Creating 100 Dummy Sales Orders...');

        for ($i = 0; $i < 100; $i++) {
            DB::transaction(function () use ($faker, $companyIds, $productVariantIds, $userIds) {
                $companyId = $faker->randomElement($companyIds);
                $createdBy = $faker->randomElement($userIds);
                $orderDate = $faker->dateTimeBetween('-1 month', 'now');

                // Create Order
                $salesOrder = SalesOrder::create([
                    'company_id' => $companyId,
                    'customer_name' => $faker->name,
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
