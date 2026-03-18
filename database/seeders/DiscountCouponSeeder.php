<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiscountCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'company_id' => 1,
                'code' => 'PROMO100K',
                'name' => 'Promo Diskon 100RB',
                'type' => 'fixed',
                'value' => 100000,
                'max_uses' => 100,
                'used_count' => 10,
                'valid_from' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'valid_until' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
            ],
            [
                'company_id' => null, // Global coupon
                'code' => 'DISC20',
                'name' => 'Diskon 20 Persen',
                'type' => 'percentage',
                'value' => 20.00,
                'max_uses' => null, // unlimited
                'used_count' => 50,
                'valid_from' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'valid_until' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            \App\Models\DiscountCoupon::create($coupon);
        }
    }
}
