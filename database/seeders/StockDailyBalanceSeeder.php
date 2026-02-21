<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StockDailyBalanceSeeder extends Seeder
{
    /**
     * Seed stock_daily_balances for all 40 product variants (30 days).
     *
     * Run: php artisan db:seed --class=StockDailyBalanceSeeder
     */
    public function run(): void
    {
        $this->command->info('📦 Generating stock_daily_balances (30 days)...');

        $today      = Carbon::today();
        $periodEnd  = $today->copy()->subDay();
        $periodStart = $periodEnd->copy()->subDays(29);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // Daily sales range per variant (min, max sold per day)
        $variantProfiles = [
            // Fast moving
            1  => ['daily' => [8, 15],  'stock' => 120],
            5  => ['daily' => [10, 20], 'stock' => 200],
            6  => ['daily' => [7, 14],  'stock' => 80],
            18 => ['daily' => [5, 12],  'stock' => 60],
            19 => ['daily' => [8, 16],  'stock' => 90],
            20 => ['daily' => [10, 18], 'stock' => 150],
            26 => ['daily' => [12, 25], 'stock' => 300],
            23 => ['daily' => [6, 12],  'stock' => 70],
            // Medium moving
            2  => ['daily' => [3, 7],   'stock' => 50],
            7  => ['daily' => [4, 8],   'stock' => 60],
            10 => ['daily' => [3, 6],   'stock' => 40],
            13 => ['daily' => [4, 7],   'stock' => 55],
            14 => ['daily' => [3, 6],   'stock' => 45],
            21 => ['daily' => [4, 8],   'stock' => 65],
            24 => ['daily' => [3, 7],   'stock' => 50],
            25 => ['daily' => [3, 6],   'stock' => 40],
            30 => ['daily' => [2, 5],   'stock' => 30],
            31 => ['daily' => [3, 6],   'stock' => 35],
            // Slow moving
            3  => ['daily' => [1, 3],   'stock' => 30],
            8  => ['daily' => [1, 3],   'stock' => 25],
            11 => ['daily' => [1, 2],   'stock' => 20],
            15 => ['daily' => [1, 2],   'stock' => 18],
            16 => ['daily' => [1, 3],   'stock' => 15],
            22 => ['daily' => [1, 2],   'stock' => 20],
            27 => ['daily' => [1, 2],   'stock' => 10],
            28 => ['daily' => [1, 3],   'stock' => 15],
            29 => ['daily' => [1, 2],   'stock' => 12],
            32 => ['daily' => [1, 2],   'stock' => 15],
            // Dead stock
            4  => ['daily' => [0, 1],   'stock' => 50],
            9  => ['daily' => [0, 0],   'stock' => 40],
            12 => ['daily' => [0, 1],   'stock' => 35],
            17 => ['daily' => [0, 0],   'stock' => 25],
            33 => ['daily' => [0, 1],   'stock' => 8],
            34 => ['daily' => [0, 0],   'stock' => 12],
            35 => ['daily' => [0, 1],   'stock' => 6],
            36 => ['daily' => [0, 0],   'stock' => 4],
            37 => ['daily' => [0, 1],   'stock' => 20],
            38 => ['daily' => [0, 0],   'stock' => 15],
            39 => ['daily' => [0, 1],   'stock' => 18],
            40 => ['daily' => [0, 0],   'stock' => 10],
        ];

        $dates = CarbonPeriod::create($periodStart, $periodEnd);
        $records = [];

        foreach ($variantProfiles as $variantId => $profile) {
            $currentStock = $profile['stock'] + rand(20, 80);

            foreach ($dates as $date) {
                $dailySold = rand($profile['daily'][0], $profile['daily'][1]);
                $dailyIn   = (rand(0, 100) < 20) ? rand(10, 50) : 0;

                $opening  = $currentStock;
                $outStock = min($dailySold, $currentStock);
                $inStock  = $dailyIn;
                $closing  = $opening + $inStock - $outStock;

                $records[] = [
                    'product_variant_id' => $variantId,
                    'date'               => $date->format('Y-m-d'),
                    'opening_stock'      => $opening,
                    'in_stock'           => $inStock,
                    'out_stock'          => $outStock,
                    'adjustment_stock'   => 0,
                    'closing_stock'      => $closing,
                    'is_locked'          => 1,
                    'created_by'         => 1,
                    'updated_by'         => 1,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                $currentStock = $closing;
            }
        }

        foreach (array_chunk($records, 500) as $chunk) {
            DB::table('stock_daily_balances')->insert($chunk);
        }

        $this->command->info('  ✅ ' . count($records) . ' stock_daily_balances records created.');
    }
}
