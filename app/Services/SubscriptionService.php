<?php

namespace App\Services;

use App\Models\Package;
use App\Models\PackagePrice;
use App\Models\Voucher;
use App\Services\Finance\AppSaleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Kalkulasi voucher ganda
     */
    public function calculateVouchers(array $data)
    {
        $packagePrice = PackagePrice::where('package_id', $data['package_id'])
            ->where('duration_months', $data['duration_months'])
            ->where('is_active', true)
            ->first();

        if (!$packagePrice) {
            return ['status' => false, 'message' => 'Harga paket tidak ditemukan atau tidak aktif.'];
        }

        $originalAmount = $packagePrice->price;
        $discountAmount = 0;
        $affiliateDiscountAmount = 0;

        // Validasi dan Hitung Voucher Reguler
        if (!empty($data['voucher_code'])) {
            $coupon = Voucher::where('code', $data['voucher_code'])->first();
            if ($coupon && $coupon->isValid()) {
                $discountAmount = $coupon->calculateDiscount($originalAmount);
            } else {
                return ['status' => false, 'message' => 'Kode voucher tidak valid, kadaluarsa, atau batas penggunaan telah habis.'];
            }
        }

        // Hitung Affiliate Discount
        $afterDiscount = $originalAmount - $discountAmount;
        if (!empty($data['affiliate_code'])) {
            $affiliator = \App\Models\Affiliator::where('code', $data['affiliate_code'])->where('is_active', true)->first();
            if ($affiliator) {
                // Gunakan discount_rate yang disetup pada tabel affiliators
                $affiliateDiscountAmount = round($afterDiscount * ($affiliator->discount_rate / 100));
            } else {
                return ['status' => false, 'message' => 'Kode Affiliate tidak ditemukan atau tidak aktif.'];
            }
        }

        $finalAmount = $originalAmount - $discountAmount - $affiliateDiscountAmount;

        return [
            'status' => true,
            'message' => 'Kalkulasi harga berhasil.',
            'data' => [
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'affiliate_discount_amount' => $affiliateDiscountAmount,
                'final_amount' => $finalAmount,
            ]
        ];
    }

    /**
     * Proses checkout
     */
    public function checkout(array $data, $user)
    {
        if (!$user->company_id) {
            return ['status' => false, 'message' => 'Anda harus memiliki perusahaan (company_id) untuk berlangganan.'];
        }

        $package = Package::find($data['package_id']);
        $packagePrice = PackagePrice::where('package_id', $data['package_id'])
            ->where('duration_months', $data['duration_months'])
            ->first();

        if (!$package || !$packagePrice) {
            return ['status' => false, 'message' => 'Paket atau durasi tidak valid.'];
        }

        try {
            $saleData = [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'plan_name' => $package->name,
                'duration_months' => $data['duration_months'],
                'is_renewal' => $user->role_id == $package->role_id ? true : false,
                'original_amount' => $packagePrice->price,
                'voucher_code' => $data['voucher_code'] ?? null,
                'affiliate_coupon_code' => $data['affiliate_code'] ?? null,
                'sale_date' => Carbon::today()->format('d/m/Y'),
                'reference_note' => 'Pembelian Langganan via Profil',
            ];

            $sale = AppSaleService::store($saleData);

            if ($sale) {
                return [
                    'status' => true,
                    'message' => 'Pesanan berhasil dibuat. Tipe: ' . $package->name,
                    'data' => $sale,
                ];
            }

            return ['status' => false, 'message' => 'Gagal membuat pesanan langganan sistem. Silakan coba lagi nanti.'];

        } catch (\Exception $e) {
            Log::error('Subscription Checkout Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Terjadi kesalahan sistem saat checkout.'];
        }
    }
}
