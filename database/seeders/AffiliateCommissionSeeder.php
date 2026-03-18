<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AffiliateCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commissions = [
            [
                'company_id' => 1,
                // Assumes AppSale ID 2 corresponds to the second inserted sale
                'app_sale_id' => 2, 
                'commission_number' => 'COM-202603-0001',
                'affiliate_name' => 'Agus Partner',
                'affiliate_coupon_code' => 'AFFJARI',
                'sale_amount' => 2700000,
                'commission_rate' => 10.00, // 10% for renewal
                'commission_amount' => 270000, 
                'status' => 'pending',
                'paid_date' => null,
                'reference_note' => 'Commission pending until app sale is confirmed',
            ],
        ];

        foreach ($commissions as $commission) {
            \App\Models\AffiliateCommission::create($commission);
        }
    }
}
