<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Finance\BusinessExpense;
use Carbon\Carbon;

class BusinessExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenses = [
            [
                'company_id' => 1,
                'expense_number' => 'EXP-202603-0001',
                'category' => 'server',
                'description' => 'AWS Hosting Cloud Services (March 2026)',
                'amount' => 500000,
                'expense_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'vendor_name' => 'Amazon Web Services',
                'reference_note' => 'Invoice #AWS-INV-001',
            ],
            [
                'company_id' => 1,
                'expense_number' => 'EXP-202603-0002',
                'category' => 'production',
                'description' => 'Marketing Ads Campaign Google',
                'amount' => 1500000,
                'expense_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'vendor_name' => 'Google Ads',
                'reference_note' => 'Invoice #GOO-INV-002',
            ],
            [
                'company_id' => 2,
                'expense_number' => 'EXP-202603-0003',
                'category' => 'other',
                'description' => 'Office Supplies',
                'amount' => 200000,
                'expense_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'vendor_name' => 'Gramedia',
                'reference_note' => 'Receipt #GRM-003',
            ],
        ];

        // Ensure the model class exists. Given the folder structure implies maybe App\Models\BusinessExpense depending on setup.
        // Assuming App\Models\BusinessExpense exists.
        foreach ($expenses as $expense) {
            \App\Models\BusinessExpense::create($expense);
        }
    }
}
