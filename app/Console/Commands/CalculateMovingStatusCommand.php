<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Stock\MovingStatusService;

class CalculateMovingStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stock:calculate-moving
                            {--company= : Specific company ID (optional, default: all companies)}
                            {--period=30 : Analysis period in days}';

    /**
     * The console command description.
     */
    protected $description = 'Calculate moving status (fast/medium/slow/dead) for all product variants based on hybrid scoring';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $companyId  = $this->option('company') ? (int) $this->option('company') : null;
        $periodDays = (int) $this->option('period');

        $scope = $companyId ? "company #{$companyId}" : 'all companies';
        $this->info("📊 Calculating moving status for {$scope} (last {$periodDays} days)...");
        $this->newLine();

        try {
            $results = MovingStatusService::calculate($companyId, $periodDays);

            if (empty($results)) {
                $this->warn('No companies with active products found.');
                return self::SUCCESS;
            }

            // Display results table
            $tableData = [];
            foreach ($results as $result) {
                if (isset($result['message'])) {
                    $tableData[] = [
                        $result['company_id'],
                        $result['total_variants'] ?? 0,
                        '-', '-', '-', '-',
                        $result['message'],
                    ];
                } else {
                    $tableData[] = [
                        $result['company_id'],
                        $result['total_variants'],
                        $result['fast'],
                        $result['medium'],
                        $result['slow'],
                        $result['dead'],
                        '✅ Done',
                    ];
                }
            }

            $this->table(
                ['Company', 'Total', '🟢 Fast', '🟡 Medium', '🟠 Slow', '🔴 Dead', 'Status'],
                $tableData
            );

            $this->newLine();
            $this->info('✅ Moving status calculation completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
