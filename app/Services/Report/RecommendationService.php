<?php

namespace App\Services\Report;

use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Report\RecommendationRepository;

// Load Models
use App\Models\RecommendationStock;
use App\Models\RecommendationStockDetail;

// Load Agent
use App\AI\Agents\StockRecommendationAgent;

class RecommendationService
{
    /**
     * Prepare DataTable for a specific analysis history.
     */
    public static function datatable()
    {
        $data = RecommendationRepository::datatable();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('analysis_date_display', function ($row) {
                // Determine format
                $dateFormatted = \Carbon\Carbon::parse($row->analysis_date)->translatedFormat('d F Y');
                $startFormatted = \Carbon\Carbon::parse($row->period_start)->translatedFormat('d M Y');
                $endFormatted = \Carbon\Carbon::parse($row->period_end)->translatedFormat('d M Y');
                
                return '
                    <div class="user-info">
                        <span class="tb-lead">' . $dateFormatted . '</span>
                        <span class="d-block fs-12px text-soft">Periode: ' . $startFormatted . ' - ' . $endFormatted . '</span>
                    </div>';
            })
            ->addColumn('cogs_balance_display', function ($row) {
                return '<span class="fw-medium">Rp ' . number_format($row->cogs_balance, 0, ',', '.') . '</span>';
            })
            ->addColumn('fast_display', function ($row) {
                return '<span class="text-success fw-bold">' . $row->total_fast . '</span>';
            })
            ->addColumn('medium_display', function ($row) {
                return '<span class="text-warning fw-bold" style="color: #f4bd0e !important;">' . $row->total_medium . '</span>';
            })
            ->addColumn('slow_display', function ($row) {
                return '<span class="fw-bold" style="color: #fd7e14;">' . $row->total_slow . '</span>';
            })
            ->addColumn('dead_display', function ($row) {
                return '<span class="text-danger fw-bold">' . $row->total_dead . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('report.stock-recommendation.detail', ['id' => $row->id]) . '" class="btn btn-dim btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Detail"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</a>
                        <a href="' . route('report.stock-recommendation.form', ['id' => $row->id]) . '" class="btn btn-dim btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a>
                        <a href="' . route('report.stock-recommendation.download-pdf', ['id' => $row->id]) . '" class="btn btn-dim btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download PDF" target="_blank"><em class="icon ni ni-download-cloud d-none d-sm-inline me-1"></em> Download</a>
                        <button type="button" class="btn btn-dim btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>
                    ';
            })
            ->addColumn('total_estimated_amount_display', function ($row) {
                return '<span class="fw-medium">Rp ' . number_format($row->total_estimated_nominal, 0, ',', '.') . '</span>';
            })
            ->rawColumns(['analysis_date_display', 'cogs_balance_display', 'fast_display', 'medium_display', 'slow_display', 'dead_display', 'action', 'total_estimated_amount_display'])
            ->make(true);
    }

    /**
     * Prepare DataTable for Form/Detail page details
     */
    public static function detailDatatable(int $historyId, \Illuminate\Http\Request $request)
    {
        $data = RecommendationRepository::datatable($historyId);
        $isDetail = empty($request->query('is_edit'));

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('product_display', function ($row) {
                $productName = $row->variant_name && $row->variant_name !== '-' ? $row->product_name . ' - ' . $row->variant_name : $row->product_name;
                $category = $row->category_name ? $row->category_name : 'Tanpa Kategori';
                return '
                    <div class="user-info">
                        <span class="tb-lead">' . $productName . '</span>
                        <span class="d-block fs-12px text-soft">' . $category . '</span>
                    </div>';
            })
        ->addColumn('current_stock_display', function ($row) {
            return '
                <div class="d-flex flex-column">
                    <div class="mb-1">
                        <span class="fs-13px fw-bold text-dark">' . number_format($row->current_stock, 0, ',', '.') . '</span>
                        <span class="fs-11px fw-normal text-soft">Saat Ini</span>
                    </div>
                    <div>
                        <span class="fs-12px text-soft"><em class="icon ni ni-cart fs-13px me-1"></em> Terjual: </span>
                        <span class="fs-12px fw-bold text-dark">' . number_format($row->total_qty_sold, 0, ',', '.') . '</span>
                    </div>
                    <div>
                        <span class="fs-12px text-soft"><em class="icon ni ni-trend-up fs-13px me-1"></em> Avg/Hari: </span>
                        <span class="fs-12px fw-bold text-dark">' . number_format($row->avg_daily_sales, 1, ',', '.') . '</span>
                    </div>
                </div>';
        })
        ->addColumn('performance_display', function ($row) {
            $badgeClass = '';
            $statusText = strtoupper($row->moving_status);
            if ($statusText == 'FAST') $badgeClass = 'bg-success-dim text-success';
            elseif ($statusText == 'MEDIUM') $badgeClass = 'bg-warning-dim text-warning';
            elseif ($statusText == 'SLOW') $badgeClass = 'bg-orange-dim text-orange';
            else $badgeClass = 'bg-danger-dim text-danger';

            return '<span class="badge ' . $badgeClass . ' fs-11px px-3 py-1">' . $statusText . '</span>';
        })
            ->addColumn('purchase_price_display', function ($row) {
                // If purchase_price was 0 during analysis, calculate from current avg if possible, otherwise keep 0
                return '<span class="fw-medium">Rp ' . number_format($row->purchase_price, 0, ',', '.') . '</span>';
            })
            ->addColumn('qty_recommendation', function ($row) use ($isDetail) {
                if ($isDetail) {
                    return '<span class="fw-bold">' . ($row->qty_restock ?? 0) . '</span>';
                }

                $value = $row->qty_restock ?? '';
                return '<input type="number" class="form-control text-center qty-input" 
                            style="width: 80px; margin: 0 auto;" 
                            data-id="' . $row->id . '" 
                            data-price="' . $row->purchase_price . '" 
                            data-original-qty="' . ($row->qty_restock ?? 0) . '"
                            min="0" value="' . $value . '">';
            })
            ->addColumn('estimated_nominal', function ($row) {
                $nominal = ($row->qty_restock ?? 0) * $row->purchase_price;
                return '<span class="fw-bold text-primary" id="est-' . $row->id . '" data-value="' . $nominal . '">Rp ' . number_format($nominal, 0, ',', '.') . '</span>';
            })
            ->rawColumns(['product_display', 'performance_display', 'purchase_price_display', 'qty_recommendation', 'estimated_nominal', 'current_stock_display'])
            ->make(true);
    }

    /**
     * Generate PDF for a specific recommendation
     */
    public static function generatePdf(int $id, int $companyId)
    {
        $history = RecommendationStock::where('id', $id)
                    ->where('company_id', $companyId)
                    ->firstOrFail();

        $details = RecommendationStockDetail::from('recommendation_stock_details as rsd')
            ->join('recommendation_stocks as rs', 'rs.id', '=', 'rsd.recommendation_stock_id')
            ->join('product_variants as pv', 'pv.id', '=', 'rsd.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('rsd.recommendation_stock_id', $id)
            ->select([
                'rsd.*',
                'p.name as product_name',
                'pv.name as variant_name',
                'c.name as category_name',
                'pv.current_stock as live_stock'
            ])
            ->get();

        $data = [
            'history' => $history,
            'details' => $details,
            'company' => \App\Models\Company::find($companyId)
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.recommendation.pdf', $data);
        $filename = 'Rekomendasi_Stok_' . \Carbon\Carbon::parse($history->analysis_date)->format('Ymd') . '.pdf';

        return [
            'pdf' => $pdf,
            'filename' => $filename
        ];
    }

    /**
     * Delete a recommendation history
     */
    public static function destroy(int $id)
    {
        try {
            $history = RecommendationStock::find($id);
            $history->delete();

            return true;
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error($th->getMessage());
            return false;
        }
    }

    /**
     * Generate AI Restock Recommendations
     * 
     * Hybrid approach:
     * 1. PHP computes optimal quantities deterministically (ROP + budget scaling + MOQ)
     * 2. AI only generates brief Indonesian descriptions for each product
     */
    public static function generateAIRecommendation(int $historyId)
    {
        $history = RecommendationStock::find($historyId);
        $cogsBalance = $history ? (float) $history->cogs_balance : 0;

        // 1. Fetch ALL product data
        $data = RecommendationRepository::datatable($historyId)->get();

        if ($data->isEmpty()) {
            return ['recommendations' => [], 'products' => []];
        }

        // 2. Pre-compute metrics for each product
        $items = $data->map(function ($item) {
            $avgDaily      = (float) $item->avg_daily_sales;
            $currentStock  = (int) $item->current_stock;
            $leadTime      = (int) $item->lead_time;
            $safetyStock   = (int) $item->safety_stock;
            $moq           = max((int) $item->moq, 1);
            $purchasePrice = (float) $item->purchase_price;
            $sellPrice     = (float) $item->sell_price;

            $daysOfStock   = $avgDaily > 0 ? round($currentStock / $avgDaily, 1) : 999;
            $reorderPoint  = (int) ceil($avgDaily * $leadTime);
            $profitMargin  = $sellPrice > 0 ? round(($sellPrice - $purchasePrice) / $sellPrice, 2) : 0;

            // Baseline: cover lead_time + safety_stock buffer
            $rawSuggested  = max($reorderPoint + $safetyStock - $currentStock, 0);
            $suggestedQty  = $moq > 1 ? (int) (ceil($rawSuggested / $moq) * $moq) : $rawSuggested;

            // Urgency
            if ($daysOfStock < 3)       $urgency = 'CRITICAL';
            elseif ($daysOfStock < 7)   $urgency = 'HIGH';
            elseif ($daysOfStock < 14)  $urgency = 'MEDIUM';
            else                        $urgency = 'LOW';

            // Priority weight: used for budget scaling decisions
            $urgencyWeight = match($urgency) {
                'CRITICAL' => 4, 'HIGH' => 3, 'MEDIUM' => 2, default => 1
            };
            $statusWeight = match(strtolower($item->moving_status)) {
                'fast' => 4, 'medium' => 3, 'slow' => 2, default => 1
            };
            $priorityScore = ($urgencyWeight * 2) + $statusWeight + ($profitMargin * 5) + ((float) $item->score * 3);

            return [
                'id'              => $item->id,
                'product'         => $item->product_name . ' ' . ($item->variant_name && $item->variant_name !== '-' ? $item->variant_name : ''),
                'status'          => $item->moving_status,
                'current_stock'   => $currentStock,
                'avg_daily_sales' => $avgDaily,
                'lead_time_days'  => $leadTime,
                'purchase_price'  => $purchasePrice,
                'sell_price'      => $sellPrice,
                'safety_stock'    => $safetyStock,
                'moq'             => $moq,
                'score'           => (float) $item->score,
                'total_revenue'   => (float) $item->total_revenue,
                'days_of_stock'   => $daysOfStock,
                'reorder_point'   => $reorderPoint,
                'profit_margin'   => $profitMargin,
                'suggested_qty'   => $suggestedQty,
                'urgency'         => $urgency,
                'priority_score'  => round($priorityScore, 2),
            ];
        })->toArray();

        // 3. Deterministic budget scaling
        $recommendations = self::computeOptimalQuantities($items, $cogsBalance);

        // 4. Build product price map for frontend Grand Total
        $products = $data->map(function ($item) {
            return [
                'id'             => $item->id,
                'purchase_price' => $item->purchase_price,
                'qty_restock'    => $item->qty_restock ?? 0,
            ];
        })->toArray();

        // 5. Ask AI for descriptions only (non-blocking: if AI fails, use fallback)
        $recommendations = self::enrichWithAIDescriptions($recommendations, $items);

        return [
            'recommendations' => $recommendations,
            'products'        => $products,
        ];
    }

    /**
     * Deterministic quantity computation:
     * 1. Start with suggested_qty for each product
     * 2. If total > budget: scale down, deprioritizing low-priority items first
     * 3. If total < 80% budget: scale up, prioritizing high-priority items first
     * 4. Round to MOQ multiples
     */
    private static function computeOptimalQuantities(array $items, float $budget): array
    {
        if ($budget <= 0) {
            return array_map(fn($item) => [
                'id' => $item['id'],
                'qty_recommendation' => 0,
                'ai_description' => 'Budget tidak tersedia.',
            ], $items);
        }

        // Step 1: Calculate total cost from suggested_qty
        $totalSuggestedCost = 0;
        foreach ($items as $item) {
            $totalSuggestedCost += $item['suggested_qty'] * $item['purchase_price'];
        }

        // Step 2: Max coverage cap per status (prevents over-ordering)
        $maxDaysCoverage = [
            'fast'   => 30,  // Fast sellers: stock for 1 month
            'medium' => 21,  // Medium: 3 weeks
            'slow'   => 14,  // Slow: 2 weeks
            'dead'   => 0,   // Dead: don't buy
        ];

        // Step 3: Determine scale factor
        $targetBudget = $budget * 0.9; // Aim for 90% of budget

        $results = [];

        if ($totalSuggestedCost <= 0) {
            // All suggested_qty are 0, distribute budget by priority
            $totalPriority = array_sum(array_column($items, 'priority_score'));

            foreach ($items as $item) {
                $share = $totalPriority > 0 ? ($item['priority_score'] / $totalPriority) : 0;
                $allocatedBudget = $targetBudget * $share;
                $qty = $item['purchase_price'] > 0 ? (int) floor($allocatedBudget / $item['purchase_price']) : 0;

                // Cap by max days coverage
                $status = strtolower($item['status']);
                $maxDays = $maxDaysCoverage[$status] ?? 14;
                $maxQty = $item['avg_daily_sales'] > 0 ? (int) ceil($item['avg_daily_sales'] * $maxDays) : 0;
                $qty = min($qty, $maxQty);

                // Round to MOQ
                $moq = $item['moq'];
                if ($moq > 1 && $qty > 0) {
                    $qty = (int) (floor($qty / $moq) * $moq);
                }

                $results[] = [
                    'id'                 => $item['id'],
                    'qty_recommendation' => max($qty, 0),
                    'ai_description'     => '',
                ];
            }
        } else {
            // Scale all suggested quantities proportionally
            $scaleFactor = $targetBudget / $totalSuggestedCost;

            foreach ($items as $item) {
                $baseQty = $item['suggested_qty'];

                if ($scaleFactor >= 1) {
                    // Under budget: scale up proportionally, weighted by priority
                    $maxPriority = max(array_column($items, 'priority_score')) ?: 1;
                    $priorityRatio = $item['priority_score'] / $maxPriority;
                    $qty = (int) round($baseQty * (1 + ($scaleFactor - 1) * $priorityRatio));
                } else {
                    // Over budget: scale down, protect CRITICAL/HIGH urgency
                    if (in_array($item['urgency'], ['CRITICAL', 'HIGH'])) {
                        $protectedFactor = max($scaleFactor, 0.7);
                        $qty = (int) round($baseQty * $protectedFactor);
                    } else {
                        $qty = (int) floor($baseQty * $scaleFactor);
                    }
                }

                // Dead stock: zero
                $status = strtolower($item['status']);
                if ($status === 'dead') {
                    $qty = 0;
                }

                // Cap by max days coverage (prevents over-ordering slow items)
                $maxDays = $maxDaysCoverage[$status] ?? 14;
                if ($item['avg_daily_sales'] > 0) {
                    $maxQty = (int) ceil($item['avg_daily_sales'] * $maxDays);
                    $qty = min($qty, $maxQty);
                }

                // Round to MOQ
                $moq = $item['moq'];
                if ($moq > 1 && $qty > 0) {
                    $qty = (int) (round($qty / $moq) * $moq);
                }

                $results[] = [
                    'id'                 => $item['id'],
                    'qty_recommendation' => max($qty, 0),
                    'ai_description'     => '',
                ];
            }
        }

        // Step 4: Final budget cap — if still over, scale down further
        $finalCost = 0;
        $lookup = [];
        foreach ($items as $item) $lookup[$item['id']] = $item;

        foreach ($results as $rec) {
            $finalCost += $rec['qty_recommendation'] * ($lookup[$rec['id']]['purchase_price'] ?? 0);
        }

        if ($finalCost > $budget) {
            $capFactor = $budget / $finalCost;
            foreach ($results as &$rec) {
                $qty = (int) floor($rec['qty_recommendation'] * $capFactor);
                $moq = $lookup[$rec['id']]['moq'] ?? 1;
                if ($moq > 1 && $qty > 0) {
                    $qty = (int) (floor($qty / $moq) * $moq);
                }
                $rec['qty_recommendation'] = max($qty, 0);
            }
            unset($rec);
        }

        return $results;
    }

    /**
     * Ask AI to generate brief Indonesian descriptions for each recommendation.
     * If AI fails, return recommendations with fallback descriptions.
     */
    private static function enrichWithAIDescriptions(array $recommendations, array $items): array
    {
        // Build a compact summary for the AI (just product + qty + key context)
        $lookup = [];
        foreach ($items as $item) $lookup[$item['id']] = $item;

        $summaryForAI = [];
        foreach ($recommendations as $rec) {
            $item = $lookup[$rec['id']] ?? null;
            if (!$item) continue;

            $summaryForAI[] = [
                'id'              => $rec['id'],
                'produk'          => $item['product'],
                'stok_sekarang'   => $item['current_stock'],
                'terjual_per_hari' => $item['avg_daily_sales'],
                'sisa_hari_stok'  => $item['days_of_stock'],
                'waktu_kirim'     => $item['lead_time_days'] . ' hari',
                'qty_rekomendasi' => $rec['qty_recommendation'],
            ];
        }

        $prompt = "Berikut data produk dan jumlah restock yang direkomendasikan:\n" . json_encode($summaryForAI) . "\n\n" .
                  "TUGAS:\n" .
                  "Tulis alasan singkat (maks 15 kata) dalam Bahasa Indonesia SEDERHANA untuk setiap produk.\n" .
                  "PENTING: Jelaskan pakai angka dari data! Jangan tulis kalimat template yang sama.\n\n" .
                  "Panduan:\n" .
                  "- Sebutkan fakta spesifik: berapa stok, berapa terjual/hari, berapa hari lagi habis, kenapa butuh segini banyak\n" .
                  "- Bahasa sehari-hari seperti ngobrol, mudah dipahami penjual toko\n" .
                  "- JANGAN pakai istilah: restock, safety stock, dead stock, moving, lead time, ROP\n\n" .
                  "Contoh BAGUS:\n" .
                  "- \"Terjual 20/hari, stok 5, perlu 200 untuk 10 hari.\"\n" .
                  "- \"Stok kosong! Biasa laku 8/hari, butuh isi 100.\"\n" .
                  "- \"Jarang laku, stok 50 masih cukup lama.\"\n" .
                  "- \"Laku 3/hari, stok tinggal 4, cepat habis.\"\n\n" .
                  "Format respons HANYA JSON array:\n" .
                  "[{ \"id\": int, \"ai_description\": \"string\" }]\n" .
                  "Tanpa markdown.";

        try {
            $agent = StockRecommendationAgent::make();
            $responseString = (string) $agent->prompt($prompt);

            $jsonResult = str_replace(['```json', '```'], '', $responseString);
            $jsonResult = trim($jsonResult);
            $descriptions = json_decode($jsonResult, true);

            if (is_array($descriptions)) {
                $descMap = [];
                foreach ($descriptions as $desc) {
                    $descMap[$desc['id']] = $desc['ai_description'] ?? '';
                }

                foreach ($recommendations as &$rec) {
                    if (isset($descMap[$rec['id']])) {
                        $rec['ai_description'] = $descMap[$rec['id']];
                    }
                }
                unset($rec);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Description Error: ' . $e->getMessage());
        }

        // Fallback: data-driven descriptions when AI is unavailable
        foreach ($recommendations as &$rec) {
            if (empty($rec['ai_description'])) {
                $item = $lookup[$rec['id']] ?? null;
                if ($item) {
                    $rec['ai_description'] = self::generateFallbackDescription($item, $rec['qty_recommendation']);
                }
            }
        }
        unset($rec);

        return $recommendations;
    }

    /**
     * Generate a data-driven fallback description when AI is unavailable.
     */
    private static function generateFallbackDescription(array $item, int $qty): string
    {
        $stock     = $item['current_stock'] ?? 0;
        $avgDaily  = $item['avg_daily_sales'] ?? 0;
        $daysLeft  = $item['days_of_stock'] ?? 999;

        if ($qty === 0) {
            if (strtolower($item['status']) === 'dead') {
                return 'Jarang laku, tidak perlu beli.';
            }
            return "Stok {$stock} masih cukup, belum perlu beli.";
        }

        if ($daysLeft < 3) {
            return "Stok tinggal {$stock}, laku {$avgDaily}/hari, segera beli {$qty}!";
        } elseif ($daysLeft < 7) {
            return "Stok {$stock} habis dalam {$daysLeft} hari, perlu tambah {$qty}.";
        } elseif ($daysLeft < 14) {
            return "Laku {$avgDaily}/hari, stok {$stock}, tambah {$qty} biar aman.";
        } else {
            return "Stok cukup, beli {$qty} untuk jaga persediaan.";
        }
    }
}
