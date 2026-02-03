<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Company;
use App\Models\ProductVariant;
use App\Models\User;
use Faker\Factory as Faker;

class PurchaseSeeder extends Seeder
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
            $this->command->warn('Companies, Product Variants, or Users missing. Skipping DummyPurchaseSeeder.');
            return;
        }

        $this->command->info('Creating 20 Dummy Purchases...');

        for ($i = 0; $i < 20; $i++) {
            DB::transaction(function () use ($faker, $companyIds, $productVariantIds, $userIds) {
                $companyId = $faker->randomElement($companyIds);
                $createdBy = $faker->randomElement($userIds);
                $purchaseDate = $faker->dateTimeBetween('-1 year', 'now');

                // Create Purchase
                $purchase = Purchase::create([
                    'company_id' => $companyId,
                    'purchase_date' => $purchaseDate,
                    'supplier_name' => $faker->company, // Supplier name from Faker
                    'total_cost' => 0, // Calculated later
                    'reference_note' => $faker->sentence,
                    'created_by' => $createdBy,
                    'updated_by' => $createdBy,
                    'created_at' => $purchaseDate,
                    'updated_at' => $purchaseDate,
                ]);

                $totalCost = 0;
                $numberOfItems = $faker->numberBetween(1, 10);

                for ($j = 0; $j < $numberOfItems; $j++) {
                    $variantId = $faker->randomElement($productVariantIds);
                    // Cost price random
                    $costPrice = $faker->numberBetween(5, 300) * 1000; // 5.000 - 300.000

                    $quantity = $faker->numberBetween(10, 100);
                    $subtotal = $costPrice * $quantity; // Although PurchaseDetail doesn't have subtotal, we need it for Purchase total

                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_variant_id' => $variantId,
                        'quantity' => $quantity,
                        'cost_price_per_item' => $costPrice,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy,
                        'created_at' => $purchaseDate,
                        'updated_at' => $purchaseDate,
                    ]);

                    $totalCost += $subtotal;
                }

                // Update total
                $purchase->update([
                    'total_cost' => $totalCost,
                ]);
            });
        }
    }
}
