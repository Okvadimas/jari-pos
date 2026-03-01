<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Services\Inventory\StockOpnameService;
use App\Repositories\Inventory\StockOpnameRepository;
use App\Models\StockOpname;
use App\Http\Requests\Inventory\StoreStockOpnameRequest;

class StockOpnameController extends Controller
{
    private $pageTitle = 'Stock Opname';

    /**
     * Index page
     */
    public function index(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth()->format('d/m/Y');
        $endDate   = Carbon::now()->endOfMonth()->format('d/m/Y');

        $data = [
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'title'     => $this->pageTitle,
            'js'        => 'resources/js/pages/inventory/stock-opname/index.js',
        ];

        return view('inventory.stock-opname.index', $data);
    }

    /**
     * DataTable data
     */
    public function datatable(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
        $status    = $request->status ?? null;

        return StockOpnameService::datatable($startDate, $endDate, $status);
    }

    /**
     * Summary data
     */
    public function summary(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $summary = StockOpnameService::getSummary($startDate, $endDate);

        return $this->successResponse('Success', [
            'total_opname'       => number_format($summary->total_opname, 0, ',', '.'),
            'total_selisih_plus' => number_format($summary->total_selisih_plus, 0, ',', '.'),
            'total_selisih_minus'=> number_format($summary->total_selisih_minus, 0, ',', '.'),
        ]);
    }

    /**
     * Show detail
     */
    public function show($id)
    {
        $opname = StockOpname::with(['company', 'details.variant.product', 'approvedByUser'])->findOrFail($id);

        $details = $opname->details->map(function ($detail) {
            $productName = optional($detail->variant->product)->name ?? '-';
            $variantName = optional($detail->variant)->name ?? '';

            return [
                'product_name'  => trim($productName . ' ' . $variantName),
                'sku'           => optional($detail->variant)->sku ?? '-',
                'system_stock'  => $detail->system_stock,
                'physical_stock'=> $detail->physical_stock,
                'difference'    => $detail->difference,
                'notes'         => $detail->notes,
            ];
        });

        return $this->successResponse('Success', [
            'opname'    => $opname,
            'details'   => $details,
            'opname_date_formatted' => Carbon::parse($opname->opname_date)->format('d M Y'),
            'approved_by_name'      => $opname->approvedByUser ? $opname->approvedByUser->name : null,
            'approved_at_formatted' => $opname->approved_at ? Carbon::parse($opname->approved_at)->format('d M Y H:i') : null,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [
            'title' => $this->pageTitle,
            'js'    => 'resources/js/pages/inventory/stock-opname/form.js',
        ];

        return view('inventory.stock-opname.form', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $opname = StockOpname::with(['details.variant.product'])->findOrFail($id);

        if ($opname->status !== StockOpname::STATUS_DRAFT) {
            return redirect()->route('inventory.stock-opname.index')
                ->with('error', 'Hanya stock opname draft yang bisa diedit');
        }

        $data = [
            'title'  => $this->pageTitle,
            'js'     => 'resources/js/pages/inventory/stock-opname/form.js',
            'opname' => $opname,
        ];

        return view('inventory.stock-opname.form', $data);
    }

    /**
     * Store or update stock opname
     */
    public function store(StoreStockOpnameRequest $request)
    {
        $validated = $request->validated();

        $process = StockOpnameService::store($validated);

        $message = !empty($validated['id'])
            ? 'Stock opname berhasil diupdate'
            : 'Stock opname berhasil ditambahkan';

        return $process
            ? $this->successResponse($message)
            : $this->errorResponse('Terjadi kesalahan di sistem');
    }

    /**
     * Approve stock opname
     */
    public function approve($id)
    {
        $process = StockOpnameService::approve($id);

        return $process
            ? $this->successResponse('Stock opname berhasil di-approve dan stok telah disesuaikan')
            : $this->errorResponse('Gagal approve stock opname');
    }

    /**
     * Cancel stock opname
     */
    public function cancel($id)
    {
        $process = StockOpnameService::cancel($id);

        return $process
            ? $this->successResponse('Stock opname berhasil dibatalkan')
            : $this->errorResponse('Gagal membatalkan stock opname');
    }

    /**
     * Soft delete stock opname
     */
    public function destroy(Request $request)
    {
        $process = StockOpnameService::destroy($request->id);

        return $process
            ? $this->successResponse('Stock opname berhasil dihapus')
            : $this->errorResponse('Terjadi kesalahan');
    }

    /**
     * Get system stock for a product variant (AJAX utility)
     */
    public function getSystemStock($productVariantId)
    {
        $stock = StockOpnameRepository::getSystemStock($productVariantId);

        return $this->successResponse('Success', ['system_stock' => $stock]);
    }
}
