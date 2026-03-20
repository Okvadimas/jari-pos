<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales = [
            [
                'company_id' => 1,
                'sale_number' => 'INV-APP-202603-0001',
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi@example.com',
                'plan_name' => 'Pro',
                'duration_months' => 12,
                'is_renewal' => false,
                'original_amount' => 1200000,
                'discount_amount' => 100000, // 100k off from coupon
                'affiliate_discount_amount' => 0,
                'final_amount' => 1100000,
                'affiliate_coupon_code' => null,
                'voucher_code' => 'PROMO100K',
                'status' => 'confirmed',
                'confirmed_by' => 1,
                'confirmed_at' => Carbon::now()->subDays(7),
                'sale_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'reference_note' => 'Paid via Virtual Account BCA',
            ],
            [
                'company_id' => 1,
                'sale_number' => 'INV-APP-202603-0002',
                'customer_name' => 'Siti Aminah',
                'customer_email' => 'siti@example.com',
                'plan_name' => 'Enterprise',
                'duration_months' => 6,
                'is_renewal' => true,
                'original_amount' => 3000000,
                'discount_amount' => 0,
                'affiliate_discount_amount' => 300000, // 10% affiliate discount
                'final_amount' => 2700000,
                'affiliate_coupon_code' => 'AFFJARI',
                'voucher_code' => null,
                'status' => 'pending',
                'confirmed_by' => null,
                'confirmed_at' => null,
                'sale_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'reference_note' => 'Waiting for transfer verification',
            ],
        ];

        foreach ($sales as $sale) {
            \App\Models\AppSale::create($sale);
        }
    }
}
