<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Services\Analytics\MovingStatusService;
use App\Repositories\Report\RecommendationRepository;
use App\Services\Report\RecommendationService;

// Load Model
use App\Models\RecommendationStock;
use App\Models\RecommendationStockDetail;

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
        $todayHistory = RecommendationStock::where('company_id', $user->company_id)
            ->whereDate('analysis_date', Carbon::today())
            ->first();

        if (!$todayHistory) {
            // Generate if not exists for today
            $results = MovingStatusService::calculate($user->company_id);
            if (!empty($results)) {
                $todayHistory = RecommendationStock::find($results[0]['history_id']);
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
     * Show the recommendation page with form to calculate recommendation stock
     */
    public function detail(Request $request, $id = null)
    {
        $user = Auth::user();
        
        if ($id) {
            $todayHistory = RecommendationStock::where('id', $id)
                ->where('company_id', $user->company_id)
                ->firstOrFail();
        } else {
            // Auto-generate or get today's analysis
            $todayHistory = RecommendationStock::where('company_id', $user->company_id)
                ->whereDate('analysis_date', Carbon::today())
                ->first();

            if (!$todayHistory) {
                // Generate if not exists for today
                $results = MovingStatusService::calculate($user->company_id);
                if (!empty($results)) {
                    $todayHistory = RecommendationStock::find($results[0]['history_id']);
                }
            }
        }

        $histories = RecommendationRepository::getHistoryList($user->company_id);

        $data = [
            'title'        => 'Detail Rekomendasi Stok',
            'css'          => 'resources/css/pages/report/recommendation/form.css',
            'js'           => 'resources/js/pages/report/recommendation/form.js',
            'histories'    => $histories,
            'todayHistory' => $todayHistory,
            'isEdit'       => false,
            'historyId'    => $todayHistory ? $todayHistory->id : null,
        ];

        return view('report.recommendation.form', $data);
    }

    /**
     * Show the recommendation page with form to calculate recommendation stock
     */
    public function form(Request $request, $id = null)
    {
        $user = Auth::user();
        
        if ($id) {
            $todayHistory = RecommendationStock::where('id', $id)
                ->where('company_id', $user->company_id)
                ->firstOrFail();
        } else {
            // Auto-generate or get today's analysis
            $todayHistory = RecommendationStock::where('company_id', $user->company_id)
                ->whereDate('analysis_date', Carbon::today())
                ->first();

            if (!$todayHistory) {
                // Generate if not exists for today
                $results = MovingStatusService::calculate($user->company_id);
                if (!empty($results)) {
                    $todayHistory = RecommendationStock::find($results[0]['history_id']);
                }
            }
        }

        $histories = RecommendationRepository::getHistoryList($user->company_id);

        $data = [
            'title'        => 'Form Rekomendasi Stok',
            'css'          => 'resources/css/pages/report/recommendation/form.css',
            'js'           => 'resources/js/pages/report/recommendation/form.js',
            'histories'    => $histories,
            'todayHistory' => $todayHistory,
            'isEdit'       => true,
            'historyId'    => $todayHistory ? $todayHistory->id : null,
        ];

        return view('report.recommendation.form', $data);
    }

    /**
     * Auto-save qty from DataTables
     */
    public function updateQty(Request $request)
    {
        try {
            $request->validate([
                'detail_id' => 'required|integer',
                'qty'       => 'required|integer|min:0',
            ]);

            RecommendationStockDetail::where('id', $request->detail_id)
                ->update(['qty_restock' => $request->qty]);

            return $this->successResponse('Berhasil mengupdate Qty');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Finalize save and calculate grand total
     */
    public function save(Request $request, $id)
    {
        try {
            $history = \App\Models\RecommendationStock::findOrFail($id);
            $items = $request->input('items', []);

            // Start a transaction just in case
            \Illuminate\Support\Facades\DB::beginTransaction();

            // First, update the provided items
            foreach ($items as $item) {
                $detailId = $item['id'];
                $qty = (int) $item['qty'];

                RecommendationStockDetail::where('id', $detailId)
                    ->where('recommendation_stock_id', $id)
                    ->update(['qty_restock' => $qty]);
            }

            // Then, recalculate total from ALL details
            $totalNominal = 0;
            // Refetch details to get updated qtys
            $details = RecommendationStockDetail::where('recommendation_stock_id', $id)->get();
            
            foreach ($details as $detail) {
                if ($detail->qty_restock > 0) {
                    $totalNominal += ($detail->qty_restock * $detail->purchase_price);
                }
            }

            $history->update(['total_estimated_nominal' => $totalNominal]);

            DB::commit();

            return $this->successResponse('Rekomendasi berhasil disimpan!', ['total_nominal' => $totalNominal]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menyimpan rekomendasi: ' . $e->getMessage(), 500);
        }
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
                return $this->errorResponse('Tidak ada data produk untuk dianalisis.', 422);
            }

            $result = $results[0]; // Single company

            return $this->successResponse('Analisis moving stock berhasil diproses.', [
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
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * DataTable for a specific analysis history.
     */
    public function datatable(Request $request)
    {
        if ($request->has('history_id')) {
            // This means it's requesting details for the form
            return RecommendationService::detailDatatable($request->history_id, clone $request);
        }

        // Otherwise, it's the index history list
        return RecommendationService::datatable();
    }

    /**
     * Get summary data for a specific history or current live summary if null.
     */
    public function summary($id = null)
    {
        if ($id) {
            $history = RecommendationStock::findOrFail($id);
            
            // Calculate dynamically from current details to ensure it's always accurate on first load
            $totalEstimatedNominal = RecommendationStockDetail::where('recommendation_stock_id', $id)
                ->where('qty_restock', '>', 0)
                ->get()
                ->sum(function($detail) {
                    return $detail->qty_restock * $detail->purchase_price;
                });
                
            return $this->successResponse('Berhasil mengambil summary', [
                'total_variants'       => (int) $history->total_variants,
                'total_fast'           => (int) $history->total_fast,
                'total_medium'         => (int) $history->total_medium,
                'total_slow'           => (int) $history->total_slow,
                'total_dead'           => (int) $history->total_dead,
                'cogs_balance'         => (float) $history->cogs_balance,
                'gross_profit_balance' => (float) $history->gross_profit_balance,
                'total_estimated_nominal' => (float) $totalEstimatedNominal,
                'period_start'         => $history->period_start ? \Carbon\Carbon::parse($history->period_start)->format('d/m/Y') : '-',
                'period_end'           => $history->period_end ? \Carbon\Carbon::parse($history->period_end)->format('d/m/Y') : '-',
                'period_days'          => (int) $history->period_days,
            ]);
        }

        $data = RecommendationRepository::getSummary();
        
        $fast = $data->firstWhere('moving_status', 'fast');
        $totalFast = $fast ? $fast->total : 0;

        $medium = $data->firstWhere('moving_status', 'medium');
        $totalMedium = $medium ? $medium->total : 0;

        $slow = $data->firstWhere('moving_status', 'slow');
        $totalSlow = $slow ? $slow->total : 0;

        $dead = $data->firstWhere('moving_status', 'dead');
        $totalDead = $dead ? $dead->total : 0;
        
        $totalVariants = $totalFast + $totalMedium + $totalSlow + $totalDead;

        return $this->successResponse('Berhasil mengambil summary', [
            'total_variants'      => $totalVariants,
            'total_fast'          => $totalFast,
            'total_medium'        => $totalMedium,
            'total_slow'          => $totalSlow,
            'total_dead'          => $totalDead
        ]);
    }

    /**
     * Generate PDF for a specific recommendation
     */
    public function downloadPdf($id)
    {
        try {
            $user = Auth::user();
            $result = RecommendationService::generatePdf($id, $user->company_id);
            
            return $result['pdf']->download($result['filename']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh PDF: ' . $e->getMessage());
        }
    }

    /**
     * Delete a recommendation history
     */
    public function destroy($id)
    {
        $process = RecommendationService::destroy($id);

        return $process ? $this->successResponse('Data rekomendasi berhasil dihapus') : $this->errorResponse('Gagal menghapus data rekomendasi');
    }

    /**
     * Get AI recommendations for a specific history.
     */
    public function getAiRecommendations($historyId)
    {
        try {
            $result = RecommendationService::generateAIRecommendation($historyId);
            
            return $this->successResponse('Berhasil mendapatkan rekomendasi AI', $result);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mendapatkan rekomendasi AI: ' . $e->getMessage(), 500);
        }
    }
}
