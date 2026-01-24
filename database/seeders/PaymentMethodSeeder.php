<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;

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
                'name'       => 'Transfer BCA',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Transfer BRI',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Transfer Mandiri',
                'type'       => 'bank_transfer',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Transfer BNI',
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
            [
                'name'       => 'LinkAja',
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
            ],
            // Debit/Credit Card
            [
                'name'       => 'Kartu Debit',
                'type'       => 'other',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name'       => 'Kartu Kredit',
                'type'       => 'other',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($paymentMethods as $method) {
            Payment::create($method);
        }
    }
}
