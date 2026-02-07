<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Company;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\TransactionNumberService;
use Carbon\Carbon;
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

        $this->command->info('Creating 100 Dummy Purchases...');

        for ($i = 0; $i < 100; $i++) {
            DB::transaction(function () use ($faker, $companyIds, $productVariantIds, $userIds) {
                $companyId = $faker->randomElement($companyIds);
                $createdBy = $faker->randomElement($userIds);
                $purchaseDate = Carbon::parse($faker->dateTimeBetween('-1 month', 'now'));

                // Generate order_number using stored procedure
                $orderNumber = TransactionNumberService::generatePurchaseOrder($companyId, $purchaseDate);

                // Create Purchase
                $purchase = Purchase::create([
                    'order_number' => $orderNumber,
                    'company_id' => $companyId,
                    'purchase_date' => $purchaseDate,
                    'supplier_name' => $faker->company,
                    'total_cost' => 0,
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
                    $costPrice = $faker->numberBetween(5, 300) * 1000;

                    $quantity = $faker->numberBetween(10, 100);
                    $subtotal = $costPrice * $quantity;

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
