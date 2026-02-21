<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            CampaignSeeder::class,
            MenuSeeder::class,
            PermissionSeeder::class,
            PaymentMethodSeeder::class,

            // Master Data POS
            UnitSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            ProductPriceSeeder::class,

            // Dummy Data
            SalesSeeder::class,
            PurchaseSeeder::class,
            PromotionSeeder::class,

            // Stock & Moving Status
            StockDailyBalanceSeeder::class,
            RecommendationStockSeeder::class,
            RecommendationStockDetailSeeder::class,
        ]);
    }
}
