<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use App\Services\Stock\MovingStatusService;
use App\Repositories\Report\RecommendationRepository;

class RecommendationController extends Controller
{
    private $pageTitle = 'Rekomendasi Stok';

    /**
     * Show the recommendation page with history list.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $histories = RecommendationRepository::getHistoryList($user->company_id);

        $data = [
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/report/recommendation/index.js',
            'histories' => $histories,
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

        $data = RecommendationRepository::datatable($historyId, $movingStatus);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('total_revenue', function ($row) {
                return 'Rp ' . number_format($row->total_revenue, 0, ',', '.');
            })
            ->editColumn('avg_daily_sales', function ($row) {
                return number_format($row->avg_daily_sales, 2, ',', '.');
            })
            ->editColumn('score', function ($row) {
                return number_format($row->score * 100, 1) . '%';
            })
            ->addColumn('product_display', function ($row) {
                $display = $row->product_name;
                if ($row->variant_name && $row->variant_name !== '-') {
                    $display .= ' — ' . $row->variant_name;
                }
                return $display;
            })
            ->addColumn('status_badge', function ($row) {
                $badges = [
                    'fast'   => '<span class="badge bg-success">Fast Moving</span>',
                    'medium' => '<span class="badge bg-warning text-dark">Medium Moving</span>',
                    'slow'   => '<span class="badge" style="background-color:#fd7e14;color:#fff;">Slow Moving</span>',
                    'dead'   => '<span class="badge bg-danger">Dead Stock</span>',
                ];
                return $badges[$row->moving_status] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
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
                'analysis_date'  => Carbon::parse($history->analysis_date)->format('d M Y'),
                'period_days'    => $history->period_days,
                'period_start'   => Carbon::parse($history->period_start)->format('d M Y'),
                'period_end'     => Carbon::parse($history->period_end)->format('d M Y'),
                'total_variants' => $history->total_variants,
                'total_fast'     => $history->total_fast,
                'total_medium'   => $history->total_medium,
                'total_slow'     => $history->total_slow,
                'total_dead'     => $history->total_dead,
            ],
        ]);
    }
}
