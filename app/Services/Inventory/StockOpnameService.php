<?php

namespace App\Services\Inventory;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Inventory\StockOpnameRepository;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;

use App\Services\Utilities\TransactionNumberService;

class StockOpnameService
{
    /**
     * Get formatted datatable
     */
    public static function datatable($startDate, $endDate, $status = null)
    {
        $data = StockOpnameRepository::datatable($startDate, $endDate, $status);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('opname_date', function ($row) {
                return Carbon::parse($row->opname_date)->format('d M Y');
            })
            ->addColumn('status_badge', function ($row) {
                $badges = [
                    'draft'     => '<span class="badge bg-warning">Draft</span>',
                    'approved'  => '<span class="badge bg-success">Approved</span>',
                    'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                ];
                return $badges[$row->status] ?? '<span class="badge bg-secondary">-</span>';
            })
            ->editColumn('total_difference', function ($row) {
                $diff = (int) $row->total_difference;
                if ($diff > 0) return '<span class="text-success">+' . $diff . '</span>';
                if ($diff < 0) return '<span class="text-danger">' . $diff . '</span>';
                return '<span class="text-muted">0</span>';
            })
            ->addColumn('action', function ($row) {
                $buttons = '<button class="btn btn-dim btn-sm btn-outline-info" onclick="detail(' . $row->id . ')"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</button> ';

                if ($row->status === 'draft') {
                    $buttons .= '<a href="' . url('inventory/stock-opname/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a> ';
                    $buttons .= '<button class="btn btn-dim btn-sm btn-outline-success" onclick="approve(' . $row->id . ')"><em class="icon ni ni-check d-none d-sm-inline me-1"></em> Approve</button> ';
                    $buttons .= '<button class="btn btn-dim btn-sm btn-outline-warning" onclick="cancel(' . $row->id . ')"><em class="icon ni ni-cross d-none d-sm-inline me-1"></em> Batal</button> ';
                    $buttons .= '<button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
                }

                return $buttons;
            })
            ->rawColumns(['action', 'status_badge', 'total_difference'])
            ->make(true);
    }

    /**
     * Get summary
     */
    public static function getSummary($startDate, $endDate)
    {
        return StockOpnameRepository::getSummary($startDate, $endDate);
    }

    /**
     * Store or update stock opname (as draft)
     */
    public static function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = Auth::user();
                $opnameDate = Carbon::createFromFormat('d/m/Y', $data['opname_date']);

                if (!empty($data['id'])) {
                    // Update existing opname - only if draft
                    $opname = StockOpname::lockForUpdate()->find($data['id']);

                    if (!$opname || $opname->status !== StockOpname::STATUS_DRAFT) {
                        throw new \Exception('Stock opname tidak ditemukan atau sudah tidak bisa diedit');
                    }

                    $opname->update([
                        'opname_date'  => $opnameDate,
                        'notes'        => $data['notes'] ?? null,
                        'updated_by'   => $user->id,
                    ]);

                    // Delete existing details (soft delete)
                    StockOpnameDetail::where('stock_opname_id', $opname->id)->delete();
                } else {
                    // Create new stock opname with auto-generated number
                    $opnameNumber = TransactionNumberService::generateStockOpnameNumber(
                        $user->company_id,
                        $opnameDate
                    );

                    $opname = StockOpname::create([
                        'opname_number' => $opnameNumber,
                        'company_id'    => $user->company_id,
                        'opname_date'   => $opnameDate,
                        'status'        => StockOpname::STATUS_DRAFT,
                        'notes'         => $data['notes'] ?? null,
                        'created_by'    => $user->id,
                    ]);
                }

                // Create detail records
                foreach ($data['details'] as $detail) {
                    $systemStock = (int) $detail['system_stock'];
                    $physicalStock = (int) $detail['physical_stock'];
                    $difference = $physicalStock - $systemStock;

                    StockOpnameDetail::create([
                        'stock_opname_id'    => $opname->id,
                        'product_variant_id' => $detail['product_variant_id'],
                        'system_stock'       => $systemStock,
                        'physical_stock'     => $physicalStock,
                        'difference'         => $difference,
                        'notes'              => $detail['notes'] ?? null,
                        'created_by'         => $user->id,
                    ]);
                }

                return $opname;
            });
        } catch (\Throwable $th) {
            Log::error('StockOpnameService::store - ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Approve stock opname and apply adjustment to stock_daily_balances
     */
    public static function approve($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = Auth::user();
                $opname = StockOpname::lockForUpdate()->with('details')->find($id);

                if (!$opname || $opname->status !== StockOpname::STATUS_DRAFT) {
                    throw new \Exception('Stock opname tidak ditemukan atau sudah diproses');
                }

                // Update status to approved
                $opname->update([
                    'status'      => StockOpname::STATUS_APPROVED,
                    'approved_by' => $user->id,
                    'approved_at' => Carbon::now(),
                    'updated_by'  => $user->id,
                ]);

                // Filter details with non-zero difference
                $adjustableDetails = $opname->details->filter(fn($d) => $d->difference != 0);

                if ($adjustableDetails->isEmpty()) {
                    return true;
                }

                $variantIds = $adjustableDetails->pluck('product_variant_id')->toArray();
                $now = Carbon::now()->format('Y-m-d H:i:s');

                // ── Bulk query #1: existing balances for opname date ──
                $existingBalances = DB::table('stock_daily_balances')
                    ->whereIn('product_variant_id', $variantIds)
                    ->where('date', $opname->opname_date)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_variant_id');

                // ── Bulk query #2: latest balances for variants without existing record ──
                $missingIds = array_diff($variantIds, $existingBalances->keys()->toArray());
                $latestBalances = collect();

                if (!empty($missingIds)) {
                    $latestBalances = DB::table('stock_daily_balances as sdb')
                        ->joinSub(
                            DB::table('stock_daily_balances')
                                ->whereIn('product_variant_id', $missingIds)
                                ->whereNull('deleted_at')
                                ->where('date', '<', $opname->opname_date)
                                ->groupBy('product_variant_id')
                                ->selectRaw('product_variant_id, MAX(date) as max_date'),
                            'latest',
                            fn($join) => $join
                                ->on('sdb.product_variant_id', '=', 'latest.product_variant_id')
                                ->on('sdb.date', '=', 'latest.max_date')
                        )
                        ->select('sdb.product_variant_id', 'sdb.closing_stock')
                        ->get()
                        ->keyBy('product_variant_id');
                }

                // ── Process in-memory: build upserts & stock corrections ──
                $upserts = [];
                $stockCorrections = []; // variant_id => adjustment

                foreach ($adjustableDetails as $detail) {
                    $vid  = $detail->product_variant_id;
                    $diff = $detail->difference;
                    $existing = $existingBalances->get($vid);

                    if ($existing) {
                        $newAdj     = $existing->adjustment_stock + $diff;
                        $newClosing = $existing->opening_stock + $existing->in_stock + $newAdj - $existing->out_stock;

                        $upserts[] = [
                            'product_variant_id' => $vid,
                            'date'               => $opname->opname_date,
                            'opening_stock'      => $existing->opening_stock,
                            'in_stock'           => $existing->in_stock,
                            'out_stock'          => $existing->out_stock,
                            'adjustment_stock'   => $newAdj,
                            'closing_stock'      => $newClosing,
                            'created_by'         => $existing->created_by,
                            'updated_by'         => $user->id,
                            'created_at'         => $existing->created_at,
                            'updated_at'         => $now,
                        ];
                    } else {
                        $opening = $latestBalances->has($vid)
                            ? (int) $latestBalances->get($vid)->closing_stock
                            : 0;

                        $upserts[] = [
                            'product_variant_id' => $vid,
                            'date'               => $opname->opname_date,
                            'opening_stock'      => $opening,
                            'in_stock'           => 0,
                            'out_stock'          => 0,
                            'adjustment_stock'   => $diff,
                            'closing_stock'      => $opening + $diff,
                            'created_by'         => $user->id,
                            'updated_by'         => $user->id,
                            'created_at'         => $now,
                            'updated_at'         => $now,
                        ];
                    }

                    $stockCorrections[$vid] = $diff;
                }

                // ── Bulk write: upsert all balances at once ──
                DB::table('stock_daily_balances')->upsert(
                    $upserts,
                    ['product_variant_id', 'date'],
                    ['adjustment_stock', 'closing_stock', 'updated_by', 'updated_at']
                );

                // ── Bulk write: update product_variants.current_stock ──
                if (!empty($stockCorrections)) {
                    $caseWhen = '';
                    $ids = [];
                    foreach ($stockCorrections as $vid => $diff) {
                        $ids[] = $vid;
                        $caseWhen .= "WHEN {$vid} THEN current_stock + ({$diff}) ";
                    }
                    $idList = implode(',', $ids);

                    DB::statement("
                        UPDATE product_variants
                        SET current_stock = CASE id {$caseWhen} END,
                            updated_at = '{$now}'
                        WHERE id IN ({$idList})
                    ");
                }

                return true;
            });
        } catch (\Throwable $th) {
            Log::error('StockOpnameService::approve - ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Cancel stock opname (only draft)
     */
    public static function cancel($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = Auth::user();
                $opname = StockOpname::lockForUpdate()->find($id);

                if (!$opname || $opname->status !== StockOpname::STATUS_DRAFT) {
                    return false;
                }

                $opname->update([
                    'status'     => StockOpname::STATUS_CANCELLED,
                    'updated_by' => $user->id,
                ]);

                return true;
            });
        } catch (\Throwable $th) {
            Log::error('StockOpnameService::cancel - ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Soft delete stock opname (only draft)
     */
    public static function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $opname = StockOpname::find($id);

                if (!$opname || $opname->status !== StockOpname::STATUS_DRAFT) {
                    return false;
                }

                // Soft delete details first
                StockOpnameDetail::where('stock_opname_id', $id)->delete();

                // Soft delete opname
                $opname->delete();

                return true;
            });
        } catch (\Throwable $th) {
            Log::error('StockOpnameService::destroy - ' . $th->getMessage());
            return false;
        }
    }
}
