<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Role;
use App\Models\StockAdjustment;
use App\Models\StockLedger;
use App\Models\StockOpname;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockControlTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private Item $itemA;
    private Item $itemB;
    private Item $itemC;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'petugas_atk',
            'label' => 'Petugas ATK',
            'description' => 'Operator Pengelola Persediaan',
        ]);

        $this->officer = User::create([
            'name' => 'Petugas ATK BPTD',
            'username' => 'petugas_atk',
            'nip' => '199203152018011002',
            'email' => 'petugas@bptd-jatim.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $category = Category::create([
            'code' => 'ATK',
            'name' => 'Alat Tulis Kantor',
            'status' => 'active',
        ]);

        $unit = Unit::create([
            'code' => 'PCS',
            'name' => 'Pcs',
            'status' => 'active',
        ]);

        // Item A: Stok Banyak / Aman (30 > min 10)
        $this->itemA = Item::create([
            'code' => 'ATK-001',
            'name' => 'Kertas A4 80gr',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Rim',
            'small_unit' => 'Rim',
            'conversion_rate' => 1,
            'current_stock' => 30,
            'minimum_stock' => 10,
            'target_stock' => 50,
            'status' => 'active',
        ]);

        // Item B: Stok Menipis (5 <= min 10)
        $this->itemB = Item::create([
            'code' => 'ATK-002',
            'name' => 'Buku Ekspedisi',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Buku',
            'small_unit' => 'Buku',
            'conversion_rate' => 1,
            'current_stock' => 5,
            'minimum_stock' => 10,
            'target_stock' => 20,
            'status' => 'active',
        ]);

        // Item C: Stok Habis (0)
        $this->itemC = Item::create([
            'code' => 'ATK-003',
            'name' => 'Map Snelhechter Plastik',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Lusin',
            'small_unit' => 'Pcs',
            'conversion_rate' => 12,
            'current_stock' => 0,
            'minimum_stock' => 24,
            'target_stock' => 60,
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_stock_control(): void
    {
        $response = $this->get(route('inventory.stock-control.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_officer_can_view_stock_control_dashboard(): void
    {
        $response = $this->actingAs($this->officer)->get(route('inventory.stock-control.index'));
        $response->assertStatus(200);
        $response->assertSee('Kendali Stok &amp; Audit Persediaan ATK', false);
        $response->assertSee('Kertas A4 80gr');
        $response->assertSee('Buku Ekspedisi');
        $response->assertSee('Map Snelhechter Plastik');
    }

    public function test_officer_can_execute_stock_adjustment_successfully(): void
    {
        $payload = [
            'adjustment_date' => now()->format('Y-m-d'),
            'reason' => 'Barang Rusak / Kadaluarsa',
            'notes' => 'Ditemukan 5 Rim rusak terkena rembesan air hujan.',
            'items' => [
                [
                    'item_id' => $this->itemA->id,
                    'actual_stock' => 25, // dari 30 dikoreksi jadi 25
                    'mode' => 'actual',
                    'unit' => 'Rim',
                    'notes' => 'Kertas basah',
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)->postJson(
            route('inventory.stock-control.adjustment.store'),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi stok item berkurang
        $this->itemA->refresh();
        $this->assertEquals(25, $this->itemA->current_stock);

        // Verifikasi transaksi StockAdjustment tercatat
        $this->assertDatabaseHas('stock_adjustments', [
            'reason' => 'Barang Rusak / Kadaluarsa',
            'total_items' => 1,
        ]);

        // Verifikasi StockLedger mencatat mutasi ADJUSTMENT
        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->itemA->id,
            'transaction_type' => 'ADJUSTMENT',
            'quantity' => 5,
            'balance_before' => 30,
            'balance_after' => 25,
        ]);
    }

    public function test_stock_adjustment_fails_when_stock_becomes_negative(): void
    {
        $payload = [
            'adjustment_date' => now()->format('Y-m-d'),
            'reason' => 'Selisih Hitung / Hilang',
            'items' => [
                [
                    'item_id' => $this->itemB->id,
                    'delta_qty' => -10, // stok saat ini 5, jika dikurang 10 maka -5 (harus ditolak)
                    'mode' => 'delta',
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)->postJson(
            route('inventory.stock-control.adjustment.store'),
            $payload
        );

        $response->assertStatus(422);

        // Pastikan stok tidak berubah
        $this->itemB->refresh();
        $this->assertEquals(5, $this->itemB->current_stock);
    }

    public function test_officer_can_reconcile_stock_opname(): void
    {
        $payload = [
            'opname_date' => now()->format('Y-m-d'),
            'conducted_by' => 'Tim Auditor BPTD',
            'notes' => 'Opname Fisik Triwulan III',
            'items' => [
                [
                    'item_id' => $this->itemA->id, // sistem: 30, fisik: 30 (Cocok)
                    'physical_stock' => 30,
                    'notes' => 'Kondisi bagus',
                ],
                [
                    'item_id' => $this->itemB->id, // sistem: 5, fisik: 7 (Temuan lebih +2)
                    'physical_stock' => 7,
                    'notes' => 'Ada 2 buku terselip di rak bawah',
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)->postJson(
            route('inventory.stock-control.opname.store'),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'matched' => 1,
            'mismatched' => 1,
        ]);

        // Verifikasi stok item B diperbarui menjadi 7
        $this->itemB->refresh();
        $this->assertEquals(7, $this->itemB->current_stock);

        // Verifikasi entri StockOpname
        $this->assertDatabaseHas('stock_opnames', [
            'conducted_by' => 'Tim Auditor BPTD',
            'total_items' => 2,
            'total_matched' => 1,
            'total_mismatched' => 1,
        ]);

        // Verifikasi StockLedger mencatat mutasi OPNAME hanya untuk barang yang berselisih (item B)
        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->itemB->id,
            'transaction_type' => 'OPNAME',
            'quantity' => 2,
            'balance_before' => 5,
            'balance_after' => 7,
        ]);
    }

    public function test_officer_can_view_adjustment_and_opname_details_json(): void
    {
        // 1. Buat Adjustment
        $adj = StockAdjustment::create([
            'adjustment_number' => 'ADJ-20261005-0001',
            'adjustment_date' => now()->format('Y-m-d'),
            'type' => 'CORRECTION',
            'total_items' => 1,
            'reason' => 'Barang Rusak',
            'user_id' => $this->officer->id,
        ]);
        $adj->details()->create([
            'item_id' => $this->itemA->id,
            'system_stock' => 30,
            'actual_stock' => 28,
            'difference' => -2,
            'unit' => 'Rim',
        ]);

        $resAdj = $this->actingAs($this->officer)->getJson(
            route('inventory.stock-control.adjustment.show', $adj)
        );
        $resAdj->assertStatus(200);
        $resAdj->assertJson(['success' => true]);
        $resAdj->assertJsonPath('data.adjustment_number', 'ADJ-20261005-0001');

        // 2. Buat Opname
        $opn = StockOpname::create([
            'opname_number' => 'OPN-20261005-0001',
            'opname_date' => now()->format('Y-m-d'),
            'status' => 'COMPLETED',
            'total_items' => 1,
            'total_matched' => 1,
            'total_mismatched' => 0,
            'conducted_by' => 'Petugas Gudang',
            'user_id' => $this->officer->id,
        ]);
        $opn->details()->create([
            'item_id' => $this->itemA->id,
            'system_stock' => 30,
            'physical_stock' => 30,
            'difference' => 0,
            'status' => 'MATCH',
        ]);

        $resOpn = $this->actingAs($this->officer)->getJson(
            route('inventory.stock-control.opname.show', $opn)
        );
        $resOpn->assertStatus(200);
        $resOpn->assertJson(['success' => true]);
        $resOpn->assertJsonPath('data.opname_number', 'OPN-20261005-0001');
    }
}
