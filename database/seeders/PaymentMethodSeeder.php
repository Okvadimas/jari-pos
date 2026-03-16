<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            // Cash
            [
                'name'       => 'Tunai',
                'type'       => 'cash',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Bank Transfer
            [
                'name'       => 'Bank BCA',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Bank BRI',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Bank Mandiri',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Bank BNI',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // E-Wallet
            [
                'name'       => 'GoPay',
                'type'       => 'e-wallet',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'OVO',
                'type'       => 'e-wallet',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'DANA',
                'type'       => 'e-wallet',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'ShopeePay',
                'type'       => 'e-wallet',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // QRIS
            [
                'name'       => 'QRIS',
                'type'       => 'other',
                'created_by' => 1,
                'updated_by' => 1,
            ]
        ];

        foreach ([1, 2, 3] as $companyId) {
            foreach ($paymentMethods as $method) {
                PaymentMethod::create(array_merge($method, ['company_id' => $companyId]));
            }
        }
    }
}
