<?php

namespace Tests\Feature\Inventory;

use Tests\TestCase;
use App\Models\User;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\ProductVariant;
use App\Services\Inventory\StockOpnameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class StockOpnameServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $variant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create FK parent records required by the schema
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('roles')->insert([
            'id' => 2,
            'name' => 'User',
            'slug' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('companies')->insert([
            'id' => 1,
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('units')->insert([
            'id' => 1,
            'name' => 'Pcs',
            'code' => 'PCS',
            'company_id' => 1,
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'id' => 1,
            'name' => 'Test Category',
            'code' => 'TST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'id' => 1,
            'category_id' => 1,
            'name' => 'Test Product',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Setup user
        $this->user = User::factory()->create([
            'company_id' => 1,
            'role_id' => 2,
        ]);
        $this->actingAs($this->user);

        // Setup a product variant
        $this->variant = ProductVariant::create([
            'product_id' => 1,
            'unit_id' => 1,
            'name' => 'Test Variant',
            'sku' => 'TEST-SKU-01',
            'current_stock' => 10,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_store_creates_draft_opname()
    {
        $data = [
            'opname_date' => '18/02/2026',
            'notes' => 'Test note for stock opname',
            'details' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'system_stock' => 10,
                    'physical_stock' => 8,
                    'notes' => 'Lost 2 items'
                ]
            ]
        ];

        $opname = StockOpnameService::store($data);

        $this->assertInstanceOf(StockOpname::class, $opname);
        $this->assertEquals(StockOpname::STATUS_DRAFT, $opname->status);
        $this->assertEquals('Test note for stock opname', $opname->notes);

        $this->assertDatabaseHas('stock_opnames', [
            'id' => $opname->id,
            'status' => 'draft'
        ]);

        $this->assertDatabaseHas('stock_opname_details', [
            'stock_opname_id' => $opname->id,
            'product_variant_id' => $this->variant->id,
            'system_stock' => 10,
            'physical_stock' => 8,
            'difference' => -2,
            'notes' => 'Lost 2 items'
        ]);
    }

    public function test_approve_applies_stock_adjustment()
    {
        // 1. Create Draft Stock Opname
        $data = [
            'opname_date' => '18/02/2026',
            'notes' => 'Adjustment test',
            'details' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'system_stock' => 10,
                    'physical_stock' => 8,
                    'notes' => 'Lost 2 items'
                ]
            ]
        ];

        $opname = StockOpnameService::store($data);

        // 2. Approve
        $result = StockOpnameService::approve($opname->id);
        $this->assertTrue($result);

        // 3. Verify status changed to approved
        $opname->refresh();
        $this->assertEquals(StockOpname::STATUS_APPROVED, $opname->status);

        // 4. Verify product variant stock updated
        $this->variant->refresh();
        $this->assertEquals(8, $this->variant->current_stock);
    }

    public function test_cancel_draft_opname()
    {
        $data = [
            'opname_date' => '18/02/2026',
            'notes' => 'Cancel test',
            'details' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'system_stock' => 10,
                    'physical_stock' => 15,
                    'notes' => 'Found 5 more!'
                ]
            ]
        ];

        $opname = StockOpnameService::store($data);

        $result = StockOpnameService::cancel($opname->id);
        $this->assertTrue($result);

        $opname->refresh();
        $this->assertEquals(StockOpname::STATUS_CANCELLED, $opname->status);
    }

    public function test_destroy_soft_deletes_opname()
    {
        $data = [
            'opname_date' => '18/02/2026',
            'notes' => 'Destroy test',
            'details' => [
                [
                    'product_variant_id' => $this->variant->id,
                    'system_stock' => 10,
                    'physical_stock' => 12,
                ]
            ]
        ];

        $opname = StockOpnameService::store($data);
        $opnameId = $opname->id;

        $result = StockOpnameService::destroy($opnameId);
        $this->assertTrue($result);

        $this->assertSoftDeleted('stock_opnames', ['id' => $opnameId]);
        $this->assertSoftDeleted('stock_opname_details', ['stock_opname_id' => $opnameId]);
    }
}
