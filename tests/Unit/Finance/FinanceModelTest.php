<?php

namespace Tests\Unit\Finance;

use Tests\TestCase;
use App\Models\BusinessExpense;
use App\Models\AppSale;
use App\Models\AffiliateCommission;
use App\Models\DiscountCoupon;

class FinanceModelTest extends TestCase
{
    /**
     * Test BusinessExpense model fillable attributes
     */
    public function test_business_expense_fillable(): void
    {
        $expense = new BusinessExpense();
        $this->assertContains('expense_number', $expense->getFillable());
        $this->assertContains('company_id', $expense->getFillable());
        $this->assertContains('category', $expense->getFillable());
        $this->assertContains('amount', $expense->getFillable());
    }

    /**
     * Test AppSale model fillable attributes
     */
    public function test_app_sale_fillable(): void
    {
        $sale = new AppSale();
        $this->assertContains('sale_number', $sale->getFillable());
        $this->assertContains('customer_name', $sale->getFillable());
        $this->assertContains('plan_name', $sale->getFillable());
        $this->assertContains('original_amount', $sale->getFillable());
        $this->assertContains('final_amount', $sale->getFillable());
        $this->assertContains('status', $sale->getFillable());
        $this->assertContains('affiliate_coupon_code', $sale->getFillable());
    }

    /**
     * Test AppSale casts
     */
    public function test_app_sale_casts(): void
    {
        $sale = new AppSale();
        $casts = $sale->getCasts();
        $this->assertArrayHasKey('is_renewal', $casts);
        $this->assertArrayHasKey('confirmed_at', $casts);
    }

    /**
     * Test AffiliateCommission model fillable attributes
     */
    public function test_affiliate_commission_fillable(): void
    {
        $commission = new AffiliateCommission();
        $this->assertContains('commission_number', $commission->getFillable());
        $this->assertContains('app_sale_id', $commission->getFillable());
        $this->assertContains('commission_rate', $commission->getFillable());
        $this->assertContains('commission_amount', $commission->getFillable());
        $this->assertContains('status', $commission->getFillable());
    }

    /**
     * Test DiscountCoupon model fillable and casts
     */
    public function test_discount_coupon_fillable_and_casts(): void
    {
        $coupon = new DiscountCoupon();
        $this->assertContains('code', $coupon->getFillable());
        $this->assertContains('type', $coupon->getFillable());
        $this->assertContains('value', $coupon->getFillable());
        $this->assertContains('is_active', $coupon->getFillable());

        $casts = $coupon->getCasts();
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertArrayHasKey('valid_from', $casts);
        $this->assertArrayHasKey('valid_until', $casts);
    }
}
