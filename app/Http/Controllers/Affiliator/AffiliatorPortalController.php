<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AffiliateCommission;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AffiliatorPortalController extends Controller
{
    public function index()
    {
        $affiliator = Auth::guard('affiliator')->user();
        
        $data = [
            'title' => 'Dashboard Mitra',
            'affiliator' => $affiliator,
            'startDate' => Carbon::now()->startOfMonth()->format('d/m/Y'),
            'endDate' => Carbon::now()->endOfMonth()->format('d/m/Y'),
            'js' => 'resources/js/pages/affiliator/portal/index.js',
        ];

        return view('affiliate.portal.index', $data);
    }

    public function datatable(Request $request)
    {
        $affiliator = Auth::guard('affiliator')->user();
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $query = AffiliateCommission::with(['appSale'])
            ->where('affiliate_coupon_code', $affiliator->code)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('sale_amount', fn($row) => 'Rp ' . number_format($row->sale_amount, 0, ',', '.'))
            ->editColumn('commission_amount', fn($row) => 'Rp ' . number_format($row->commission_amount, 0, ',', '.'))
            ->editColumn('commission_rate', fn($row) => $row->commission_rate . '%')
            ->editColumn('status', function($row) {
                $class = $row->status == 'paid' ? 'success' : ($row->status == 'pending' ? 'warning' : 'danger');
                $text = $row->status == 'paid' ? 'Dibayar' : ($row->status == 'pending' ? 'Pending' : 'Batal');
                return '<span class="badge badge-dot badge-' . $class . '">' . $text . '</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function summary(Request $request)
    {
        $affiliator = Auth::guard('affiliator')->user();
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $stats = AffiliateCommission::where('affiliate_coupon_code', $affiliator->code)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('
                COUNT(*) as total_transaksi,
                SUM(sale_amount) as total_penjualan,
                SUM(commission_amount) as total_komisi,
                SUM(CASE WHEN status = "pending" THEN commission_amount ELSE 0 END) as komisi_pending,
                SUM(CASE WHEN status = "paid" THEN commission_amount ELSE 0 END) as komisi_paid
            ')
            ->first();

        return response()->json([
            'status' => true,
            'data' => [
                'total_transaksi' => number_format($stats->total_transaksi ?: 0, 0, ',', '.'),
                'total_penjualan' => 'Rp ' . number_format($stats->total_penjualan ?: 0, 0, ',', '.'),
                'total_komisi' => 'Rp ' . number_format($stats->total_komisi ?: 0, 0, ',', '.'),
                'komisi_pending' => 'Rp ' . number_format($stats->komisi_pending ?: 0, 0, ',', '.'),
                'komisi_paid' => 'Rp ' . number_format($stats->komisi_paid ?: 0, 0, ',', '.'),
            ]
        ]);
    }
}
