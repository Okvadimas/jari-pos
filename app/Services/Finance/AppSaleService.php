<?php

namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Finance\AppSaleRepository;
use App\Models\AppSale;
use App\Models\AffiliateCommission;
use App\Models\DiscountCoupon;
use App\Services\Utilities\TransactionNumberService;

class AppSaleService
{
    public static function datatable($startDate, $endDate)
    {
        $data = AppSaleRepository::datatable($startDate, $endDate);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('sale_date', function ($row) {
                return Carbon::parse($row->sale_date)->format('d M Y');
            })
            ->editColumn('original_amount', function ($row) {
                return 'Rp ' . number_format($row->original_amount, 0, ',', '.');
            })
            ->editColumn('final_amount', function ($row) {
                return 'Rp ' . number_format($row->final_amount, 0, ',', '.');
            })
            ->editColumn('status', function ($row) {
                $labels = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'confirmed' => '<span class="badge bg-success">Dikonfirmasi</span>',
                    'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>',
                ];
                return $labels[$row->status] ?? '<span class="badge bg-secondary">' . $row->status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '<button class="btn btn-dim btn-sm btn-outline-info" onclick="detail(' . $row->id . ')"><em class="icon ni ni-eye d-none d-sm-inline me-1"></em> Detail</button> ';
                if ($row->status === 'pending') {
                    $actions .= '<button class="btn btn-dim btn-sm btn-outline-success" onclick="konfirmasi(' . $row->id . ')"><em class="icon ni ni-check d-none d-sm-inline me-1"></em> Konfirmasi</button> ';
                }
                $actions .= '<a href="' . url('finance/app-sale/edit', $row->id) . '" class="btn btn-dim btn-sm btn-outline-primary"><em class="icon ni ni-edit d-none d-sm-inline me-1"></em> Edit</a> ';
                $actions .= '<button class="btn btn-dim btn-sm btn-outline-danger" onclick="hapus(' . $row->id . ')"><em class="icon ni ni-trash d-none d-sm-inline me-1"></em> Hapus</button>';
                return $actions;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public static function getSummary($startDate, $endDate)
    {
        return AppSaleRepository::getSummary($startDate, $endDate);
    }

    public static function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = Auth::user();
                $saleDate = Carbon::createFromFormat('d/m/Y', $data['sale_date']);

                $originalAmount = $data['original_amount'];
                $discountAmount = 0;
                $affiliateDiscountAmount = 0;

                // Calculate discount coupon
                if (!empty($data['discount_coupon_code'])) {
                    $coupon = DiscountCoupon::where('code', $data['discount_coupon_code'])->first();
                    if ($coupon && $coupon->isValid()) {
                        $discountAmount = $coupon->calculateDiscount($originalAmount);
                    }
                }

                // Calculate affiliate discount (20% of price after discount)
                $afterDiscount = $originalAmount - $discountAmount;
                if (!empty($data['affiliate_coupon_code'])) {
                    $affiliateDiscountAmount = round($afterDiscount * 0.20);
                }

                $finalAmount = $originalAmount - $discountAmount - $affiliateDiscountAmount;

                if (!empty($data['id'])) {
                    $sale = AppSale::lockForUpdate()->find($data['id']);
                    if (!$sale) throw new \Exception('Data penjualan tidak ditemukan');

                    $sale->update([
                        'customer_name' => $data['customer_name'],
                        'customer_email' => $data['customer_email'] ?? null,
                        'plan_name' => $data['plan_name'],
                        'duration_months' => $data['duration_months'],
                        'is_renewal' => $data['is_renewal'] ?? false,
                        'original_amount' => $originalAmount,
                        'discount_amount' => $discountAmount,
                        'affiliate_discount_amount' => $affiliateDiscountAmount,
                        'final_amount' => $finalAmount,
                        'affiliate_coupon_code' => $data['affiliate_coupon_code'] ?? null,
                        'discount_coupon_code' => $data['discount_coupon_code'] ?? null,
                        'sale_date' => $saleDate,
                        'reference_note' => $data['reference_note'] ?? null,
                        'updated_by' => $user->id,
                    ]);
                } else {
                    $saleNumber = TransactionNumberService::generateAppSale($user->company_id, $saleDate);

                    $sale = AppSale::create([
                        'sale_number' => $saleNumber,
                        'company_id' => $user->company_id,
                        'customer_name' => $data['customer_name'],
                        'customer_email' => $data['customer_email'] ?? null,
                        'plan_name' => $data['plan_name'],
                        'duration_months' => $data['duration_months'],
                        'is_renewal' => $data['is_renewal'] ?? false,
                        'original_amount' => $originalAmount,
                        'discount_amount' => $discountAmount,
                        'affiliate_discount_amount' => $affiliateDiscountAmount,
                        'final_amount' => $finalAmount,
                        'affiliate_coupon_code' => $data['affiliate_coupon_code'] ?? null,
                        'discount_coupon_code' => $data['discount_coupon_code'] ?? null,
                        'status' => 'pending',
                        'sale_date' => $saleDate,
                        'reference_note' => $data['reference_note'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }

                return $sale;
            });
        } catch (\Throwable $th) {
            Log::error('AppSaleService::store - ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Confirm a pending app sale and auto-create affiliate commission
     */
    public static function confirm($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = Auth::user();
                $sale = AppSale::lockForUpdate()->find($id);

                if (!$sale || $sale->status !== 'pending') return false;

                $sale->update([
                    'status' => 'confirmed',
                    'confirmed_by' => $user->id,
                    'confirmed_at' => now(),
                ]);

                // Auto-create affiliate commission if affiliate coupon is used
                if (!empty($sale->affiliate_coupon_code)) {
                    $commissionRate = $sale->is_renewal ? 10 : 20;
                    $saleAmount = $sale->final_amount;
                    $commissionAmount = round($saleAmount * ($commissionRate / 100));

                    $commissionNumber = TransactionNumberService::generateAffiliateCommission($sale->company_id, Carbon::parse($sale->sale_date));

                    AffiliateCommission::create([
                        'commission_number' => $commissionNumber,
                        'company_id' => $sale->company_id,
                        'app_sale_id' => $sale->id,
                        'affiliate_name' => $sale->affiliate_coupon_code,
                        'affiliate_coupon_code' => $sale->affiliate_coupon_code,
                        'sale_amount' => $saleAmount,
                        'commission_rate' => $commissionRate,
                        'commission_amount' => $commissionAmount,
                        'status' => 'pending',
                        'created_by' => $user->id,
                    ]);
                }

                // Increment discount coupon usage
                if (!empty($sale->discount_coupon_code)) {
                    DiscountCoupon::where('code', $sale->discount_coupon_code)->increment('used_count');
                }

                return $sale;
            });
        } catch (\Throwable $th) {
            Log::error('AppSaleService::confirm - ' . $th->getMessage());
            return false;
        }
    }

    public static function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $sale = AppSale::find($id);
                if (!$sale) return false;

                // Also delete related affiliate commission
                AffiliateCommission::where('app_sale_id', $id)->delete();

                $sale->delete();
                return true;
            });
        } catch (\Throwable $th) {
            Log::error('AppSaleService::destroy - ' . $th->getMessage());
            return false;
        }
    }
}
