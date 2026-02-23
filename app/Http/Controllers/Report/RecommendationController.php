<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use App\Services\Analytics\MovingStatusService;
use App\Repositories\Report\RecommendationRepository;
use App\Services\Report\RecommendationService;

class RecommendationController extends Controller
{
    private $pageTitle = 'Rekomendasi Stok';

    /**
     * Show the recommendation page with history list.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Auto-generate or get today's analysis
        $todayHistory = \App\Models\RecommendationStock::where('company_id', $user->company_id)
            ->whereDate('analysis_date', Carbon::today())
            ->first();

        if (!$todayHistory) {
            // Generate if not exists for today
            $results = MovingStatusService::calculate($user->company_id);
            if (!empty($results)) {
                $todayHistory = \App\Models\RecommendationStock::find($results[0]['history_id']);
            }
        }

        $histories = RecommendationRepository::getHistoryList($user->company_id);

        $data = [
            'title'        => $this->pageTitle,
            'css'          => 'resources/css/pages/report/recommendation/index.css',
            'js'           => 'resources/js/pages/report/recommendation/index.js',
            'histories'    => $histories,
            'todayHistory' => $todayHistory,
        ];

        return view('report.recommendation.index', $data);
    }

    /**
     * Trigger moving stock analysis for the current company.
     */
    public function generate(Request $request)
    {
        try {
            $user = Auth::user();
            $periodDays = $request->input('period_days', 30);

            $results = MovingStatusService::calculate($user->company_id, $periodDays);

            if (empty($results)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data produk untuk dianalisis.',
                ], 422);
            }

            $result = $results[0]; // Single company

            return response()->json([
                'success'    => true,
                'message'    => 'Analisis moving stock berhasil diproses.',
                'history_id' => $result['history_id'] ?? null,
                'summary'    => [
                    'total'  => $result['total_variants'] ?? 0,
                    'fast'   => $result['fast'] ?? 0,
                    'medium' => $result['medium'] ?? 0,
                    'slow'   => $result['slow'] ?? 0,
                    'dead'   => $result['dead'] ?? 0,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DataTable for a specific analysis history.
     */
    public function datatable(Request $request)
    {
        $historyId    = $request->input('history_id');
        $movingStatus = $request->input('moving_status');

        if (!$historyId) {
            return DataTables::of(collect([]))->make(true);
        }

        return RecommendationService::datatable($historyId, $movingStatus);
    }

    /**
     * Get summary data for a specific history.
     */
    public function summary($id)
    {
        $history = RecommendationRepository::getSummary($id);

        if (!$history) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'analysis_date'       => Carbon::parse($history->analysis_date)->format('d M Y'),
                'period_days'         => $history->period_days,
                'period_start'        => Carbon::parse($history->period_start)->format('d M Y'),
                'period_end'          => Carbon::parse($history->period_end)->format('d M Y'),
                'total_variants'      => $history->total_variants,
                'total_fast'          => $history->total_fast,
                'total_medium'        => $history->total_medium,
                'total_slow'          => $history->total_slow,
                'total_dead'          => $history->total_dead,
                'cogs_balance'        => $history->cogs_balance,
                'gross_profit_balance'=> $history->gross_profit_balance,
            ],
        ]);
    }
}
