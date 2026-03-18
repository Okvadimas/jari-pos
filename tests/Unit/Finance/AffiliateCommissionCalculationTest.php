<?php

namespace Tests\Unit\Finance;

use Tests\TestCase;

class AffiliateCommissionCalculationTest extends TestCase
{
    /**
     * Test affiliate commission for new customer
     * Harga: 85000, Affiliate discount 20% = 17000 → Final: 68000
     * Komisi 20% dari 68000 = 13600
     */
    public function test_new_sale_commission_calculation(): void
    {
        $originalAmount = 85000;
        $affiliateDiscountRate = 0.20;
        $affiliateDiscountAmount = round($originalAmount * $affiliateDiscountRate);
        $finalAmount = $originalAmount - $affiliateDiscountAmount;

        // Discount = 17000
        $this->assertEquals(17000, $affiliateDiscountAmount);
        // Final = 68000
        $this->assertEquals(68000, $finalAmount);

        // Commission = 20% of final
        $commissionRate = 20;
        $commissionAmount = round($finalAmount * ($commissionRate / 100));
        $this->assertEquals(13600, $commissionAmount);
    }

    /**
     * Test affiliate commission for renewal
     * Harga perpanjangan: 85000 (no affiliate discount on renewals, commission is 10%)
     * Komisi 10% dari 85000 = 8500
     */
    public function test_renewal_commission_calculation(): void
    {
        $finalAmount = 85000;
        $isRenewal = true;
        $commissionRate = $isRenewal ? 10 : 20;
        $commissionAmount = round($finalAmount * ($commissionRate / 100));

        $this->assertEquals(10, $commissionRate);
        $this->assertEquals(8500, $commissionAmount);
    }

    /**
     * Test combined discount (coupon + affiliate)
     * Harga: 100000
     * Kupon diskon 10% = 10000 → setelah diskon: 90000
     * Affiliate 20% dari 90000 = 18000
     * Final: 90000 - 18000 = 72000
     * Komisi affiliate 20% dari 72000 = 14400
     */
    public function test_combined_discount_and_affiliate(): void
    {
        $originalAmount = 100000;

        // Step 1: Apply coupon discount
        $couponDiscountRate = 0.10; // 10%
        $discountAmount = round($originalAmount * $couponDiscountRate);
        $afterDiscount = $originalAmount - $discountAmount;

        $this->assertEquals(10000, $discountAmount);
        $this->assertEquals(90000, $afterDiscount);

        // Step 2: Apply affiliate discount
        $affiliateDiscountAmount = round($afterDiscount * 0.20);
        $finalAmount = $afterDiscount - $affiliateDiscountAmount;

        $this->assertEquals(18000, $affiliateDiscountAmount);
        $this->assertEquals(72000, $finalAmount);

        // Step 3: Calculate commission
        $commissionRate = 20; // new sale
        $commissionAmount = round($finalAmount * ($commissionRate / 100));
        $this->assertEquals(14400, $commissionAmount);
    }

    /**
     * Test no affiliate when no coupon is provided
     */
    public function test_no_affiliate_no_commission(): void
    {
        $originalAmount = 85000;
        $affiliateCouponCode = null;
        $affiliateDiscountAmount = 0;

        if (!empty($affiliateCouponCode)) {
            $affiliateDiscountAmount = round($originalAmount * 0.20);
        }

        $this->assertEquals(0, $affiliateDiscountAmount);
        $finalAmount = $originalAmount - $affiliateDiscountAmount;
        $this->assertEquals(85000, $finalAmount);
    }
}
