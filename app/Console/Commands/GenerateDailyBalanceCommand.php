<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Stock\StockDailyBalanceService;

class GenerateDailyBalanceCommand extends Command
{
    protected $signature = 'stock:generate-daily-balance {--company= : Process specific company ID}';
    protected $description = 'Generate stock daily balances: close yesterday, validate current_stock, open today';

    public function handle(): int
    {
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $scope = $companyId ? "company #{$companyId}" : 'all companies';

        $this->info("📦 Generate Daily Balance — " . now()->format('Y-m-d') . " ({$scope})");
        $this->newLine();

        try {
            $results = StockDailyBalanceService::generate($companyId);

            if (empty($results)) {
                $this->warn('No companies found.');
                return self::SUCCESS;
            }

            // Display results table
            $tableData = [];
            foreach ($results as $result) {
                if (isset($result['error'])) {
                    $tableData[] = [
                        $result['company_id'],
                        $result['company_name'],
                        $result['processed'],
                        $result['corrected'],
                        '❌ ' . $result['error'],
                    ];
                } else {
                    $tableData[] = [
                        $result['company_id'],
                        $result['company_name'],
                        $result['processed'],
                        $result['corrected'],
                        isset($result['message']) ? "⚠️ {$result['message']}" : '✅ Done',
                    ];
                }
            }

            $this->table(
                ['ID', 'Company', 'Processed', 'Corrected', 'Status'],
                $tableData
            );

            $this->newLine();
            $this->info('✅ Daily balance generation complete.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
