<?php

namespace Tests\Unit\Finance;

use Tests\TestCase;
use App\Models\Voucher;

class VoucherTest extends TestCase
{
    /**
     * Test percentage discount calculation
     */
    public function test_percentage_discount_calculation(): void
    {
        $coupon = new Voucher([
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ]);

        // 20% of 85000 = 17000
        $this->assertEquals(17000, $coupon->calculateDiscount(85000));

        // 20% of 100000 = 20000
        $this->assertEquals(20000, $coupon->calculateDiscount(100000));
    }

    /**
     * Test fixed discount calculation
     */
    public function test_fixed_discount_calculation(): void
    {
        $coupon = new Voucher([
            'type' => 'fixed',
            'value' => 10000,
            'is_active' => true,
        ]);

        // Fixed 10000 off from 85000
        $this->assertEquals(10000, $coupon->calculateDiscount(85000));

        // Fixed discount cannot exceed price
        $this->assertEquals(5000, $coupon->calculateDiscount(5000));
    }

    /**
     * Test coupon validity - active coupon
     */
    public function test_active_coupon_is_valid(): void
    {
        $coupon = new Voucher([
            'is_active' => true,
            'max_uses' => 100,
            'used_count' => 10,
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5),
        ]);

        $this->assertTrue($coupon->isValid());
    }

    /**
     * Test coupon validity - inactive coupon
     */
    public function test_inactive_coupon_is_invalid(): void
    {
        $coupon = new Voucher([
            'is_active' => false,
            'max_uses' => 100,
            'used_count' => 10,
        ]);

        $this->assertFalse($coupon->isValid());
    }

    /**
     * Test coupon validity - max uses reached
     */
    public function test_max_uses_reached_is_invalid(): void
    {
        $coupon = new Voucher([
            'is_active' => true,
            'max_uses' => 10,
            'used_count' => 10,
        ]);

        $this->assertFalse($coupon->isValid());
    }

    /**
     * Test coupon validity - expired
     */
    public function test_expired_coupon_is_invalid(): void
    {
        $coupon = new Voucher([
            'is_active' => true,
            'valid_from' => now()->subDays(10),
            'valid_until' => now()->subDays(1),
        ]);

        $this->assertFalse($coupon->isValid());
    }

    /**
     * Test coupon validity - not yet valid
     */
    public function test_not_yet_valid_coupon_is_invalid(): void
    {
        $coupon = new Voucher([
            'is_active' => true,
            'valid_from' => now()->addDays(5),
            'valid_until' => now()->addDays(10),
        ]);

        $this->assertFalse($coupon->isValid());
    }

    /**
     * Test coupon validity - unlimited uses (max_uses = null)
     */
    public function test_unlimited_uses_coupon_is_valid(): void
    {
        $coupon = new Voucher([
            'is_active' => true,
            'max_uses' => null,
            'used_count' => 999,
        ]);

        $this->assertTrue($coupon->isValid());
    }
}
