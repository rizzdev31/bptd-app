<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\Role;
use App\Models\StockIn;
use App\Models\StockLedger;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private Supplier $supplier;
    private Item $itemDus;
    private Item $itemPcs;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'petugas_atk',
            'label' => 'Petugas ATK',
            'description' => 'Operator Pengelola Persediaan',
        ]);

        $this->officer = User::create([
            'name' => 'Petugas Pengadaan BPTD',
            'username' => 'petugas_pengadaan',
            'nip' => '199001012015011001',
            'email' => 'pengadaan@bptd-jatim.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $category = Category::create([
            'code' => 'ATK',
            'name' => 'Alat Tulis Kantor',
            'status' => 'active',
        ]);

        $unitPcs = Unit::create([
            'code' => 'PCS',
            'name' => 'Pcs',
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'CV. Jaya Logistik Kemenhub',
            'contact_person' => 'Bapak Budi Santoso',
            'phone' => '081234567890',
            'email' => 'jaya@logistik.id',
            'address' => 'Jl. Ahmad Yani No. 10 Surabaya',
            'tax_number' => '01.234.567.8-012.000',
            'status' => 'active',
        ]);

        // Item 1: Multi unit (1 Dus = 12 Pcs), stok awal 10 Pcs
        $this->itemDus = Item::create([
            'code' => 'ATK-DUS-01',
            'name' => 'Pulpen Gel Hitam 0.5mm',
            'category_id' => $category->id,
            'unit_id' => $unitPcs->id,
            'unit' => 'Dus',
            'small_unit' => 'Pcs',
            'conversion_rate' => 12,
            'minimum_stock' => 24,
            'target_stock' => 120,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        // Item 2: Single unit (Pcs), stok awal 50 Pcs
        $this->itemPcs = Item::create([
            'code' => 'ATK-PCS-01',
            'name' => 'Buku Ekspedisi Folio 100 Lembar',
            'category_id' => $category->id,
            'unit_id' => $unitPcs->id,
            'unit' => 'Pcs',
            'small_unit' => 'Pcs',
            'conversion_rate' => 1,
            'minimum_stock' => 10,
            'target_stock' => 50,
            'current_stock' => 50,
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_procurement_page(): void
    {
        $response = $this->get(route('inventory.procurement.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_officer_can_view_procurement_index(): void
    {
        $response = $this->actingAs($this->officer)->get(route('inventory.procurement.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengadaan &amp; Penerimaan ATK', false);
        $response->assertSee('CV. Jaya Logistik Kemenhub');
    }

    public function test_officer_can_store_procurement_as_ordered_without_changing_stock(): void
    {
        $payload = [
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'PO-REF/2026/01',
            'status' => 'ordered',
            'notes' => 'Pengadaan rutin triwulan ATK BPTD',
            'items' => [
                [
                    'item_id' => $this->itemDus->id,
                    'quantity' => 5, // 5 Dus @ 12 = 60 Pcs
                    'unit' => 'Dus',
                    'conversion_factor' => 12,
                    'unit_price' => 36000, // Rp 36.000 / Dus
                ],
                [
                    'item_id' => $this->itemPcs->id,
                    'quantity' => 20, // 20 Pcs
                    'unit' => 'Pcs',
                    'conversion_factor' => 1,
                    'unit_price' => 15000, // Rp 15.000 / Pcs
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)
            ->post(route('inventory.procurement.store'), $payload);

        $response->assertRedirect(route('inventory.procurement.index', ['tab' => 'procurement']));

        $this->assertDatabaseHas('procurements', [
            'supplier_id' => $this->supplier->id,
            'status' => 'ordered',
            'total_amount' => (5 * 36000) + (20 * 15000), // 180.000 + 300.000 = 480.000
        ]);

        // Sesuai PRD Seksi 23: "Procurement yang belum diterima tidak menambah stock."
        $this->assertEquals(10, $this->itemDus->fresh()->current_stock);
        $this->assertEquals(50, $this->itemPcs->fresh()->current_stock);

        // Tidak ada catatan mutasi fisik atau ledger saat masih ordered
        $this->assertEquals(0, StockIn::count());
        $this->assertEquals(0, StockLedger::where('transaction_type', 'IN')->count());
    }

    public function test_officer_can_receive_procurement_and_increase_physical_stock(): void
    {
        // 1. Buat procurement ordered terlebih dahulu
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-0001',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'ordered',
            'total_amount' => 240000,
            'user_id' => $this->officer->id,
        ]);

        $procurement->details()->create([
            'item_id' => $this->itemDus->id,
            'item_name' => $this->itemDus->name,
            'item_code' => $this->itemDus->code,
            'quantity' => 2, // 2 Dus
            'unit' => 'Dus',
            'conversion_factor' => 12,
            'base_quantity' => 24, // 24 Pcs fisik
            'unit_price' => 36000,
            'subtotal' => 72000,
        ]);

        // 2. Konfirmasi penerimaan barang
        $response = $this->actingAs($this->officer)
            ->postJson(route('inventory.procurement.receive', $procurement), [
                'invoice_number' => 'FAKTUR-VENDOR-999',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Status procurement berubah menjadi received
        $procurement->refresh();
        $this->assertEquals('received', $procurement->status);
        $this->assertEquals('FAKTUR-VENDOR-999', $procurement->invoice_number);
        $this->assertNotNull($procurement->received_at);

        // Stok fisik bertambah (Awal 10 + 24 = 34 Pcs)
        $this->assertEquals(34, $this->itemDus->fresh()->current_stock);

        // Record StockIn terbentuk
        $this->assertDatabaseHas('stock_ins', [
            'procurement_id' => $procurement->id,
            'source' => 'Procurement',
            'total_quantity' => 24,
        ]);

        // StockLedger (IN) tercatat
        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->itemDus->id,
            'transaction_type' => 'IN',
            'quantity' => 24,
            'balance_before' => 10,
            'balance_after' => 34,
            'reference_number' => $procurement->procurement_number,
        ]);
    }

    public function test_cannot_receive_procurement_twice_preventing_double_stock_addition(): void
    {
        // Setup procurement yang sudah berstatus received
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-0002',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'received',
            'total_amount' => 50000,
            'user_id' => $this->officer->id,
            'received_at' => now(),
        ]);

        // Coba terima lagi
        $response = $this->actingAs($this->officer)
            ->postJson(route('inventory.procurement.receive', $procurement), []);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_officer_can_store_procurement_directly_as_received(): void
    {
        $payload = [
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'INV-DIRECT-01',
            'status' => 'received',
            'items' => [
                [
                    'item_id' => $this->itemPcs->id,
                    'quantity' => 15,
                    'unit' => 'Pcs',
                    'conversion_factor' => 1,
                    'unit_price' => 12000,
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)
            ->postJson(route('inventory.procurement.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Stok langsung bertambah dari 50 menjadi 65 Pcs
        $this->assertEquals(65, $this->itemPcs->fresh()->current_stock);

        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->itemPcs->id,
            'transaction_type' => 'IN',
            'quantity' => 15,
            'balance_before' => 50,
            'balance_after' => 65,
        ]);
    }

    public function test_officer_can_cancel_ordered_procurement(): void
    {
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-0003',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'ordered',
            'total_amount' => 100000,
            'user_id' => $this->officer->id,
        ]);

        $response = $this->actingAs($this->officer)
            ->postJson(route('inventory.procurement.cancel', $procurement), [
                'reason' => 'Vendor kehabisan stok barang',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $procurement->fresh()->status);
    }

    public function test_officer_can_record_direct_stock_in(): void
    {
        $payload = [
            'date' => date('Y-m-d'),
            'source' => 'Hibah',
            'supplier_name' => 'Balai Pengelola Transportasi',
            'reference_number' => 'ND-123/BPTD/2026',
            'notes' => 'Penerimaan hibah persediaan ATK',
            'items' => [
                [
                    'item_id' => $this->itemDus->id,
                    'quantity' => 1, // 1 Dus = 12 Pcs
                    'unit' => 'Dus',
                    'conversion_factor' => 12,
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)
            ->postJson(route('inventory.procurement.direct-stock-in.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Stok bertambah 12 Pcs (10 + 12 = 22 Pcs)
        $this->assertEquals(22, $this->itemDus->fresh()->current_stock);

        $this->assertDatabaseHas('stock_ins', [
            'source' => 'Hibah',
            'total_quantity' => 12,
        ]);
    }

    public function test_officer_can_store_supplier_via_ajax(): void
    {
        $payload = [
            'name' => 'PT Mitra Sarana Transportasi',
            'contact_person' => 'Ibu Rina Wijaya',
            'phone' => '085678901234',
            'email' => 'rina@mitrasarana.com',
            'address' => 'Jl. Pemuda No. 45 Surabaya',
            'tax_number' => '02.345.678.9-023.000',
        ];

        $response = $this->actingAs($this->officer)
            ->postJson(route('inventory.procurement.supplier.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT Mitra Sarana Transportasi',
            'contact_person' => 'Ibu Rina Wijaya',
        ]);
    }

    public function test_officer_can_view_procurement_print_pages(): void
    {
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-0004',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'received',
            'total_amount' => 120000,
            'user_id' => $this->officer->id,
            'received_at' => now(),
        ]);

        $procurement->details()->create([
            'item_id' => $this->itemPcs->id,
            'item_name' => $this->itemPcs->name,
            'item_code' => $this->itemPcs->code,
            'quantity' => 10,
            'unit' => 'Pcs',
            'conversion_factor' => 1,
            'base_quantity' => 10,
            'unit_price' => 12000,
            'subtotal' => 120000,
        ]);

        // Cetak PO
        $responsePO = $this->actingAs($this->officer)
            ->get(route('inventory.procurement.print', ['procurement' => $procurement->id, 'type' => 'po']));
        $responsePO->assertStatus(200);
        $responsePO->assertSee('SURAT PESANAN PENGADAAN BARANG (PURCHASE ORDER)');

        // Cetak BAPHP
        $responseBAPHP = $this->actingAs($this->officer)
            ->get(route('inventory.procurement.print', ['procurement' => $procurement->id, 'type' => 'baphp']));
        $responseBAPHP->assertStatus(200);
        $responseBAPHP->assertSee('BERITA ACARA PENERIMAAN HASIL PENGADAAN (BAPHP)');
    }

    public function test_multi_unit_procurement_calculates_physical_stock_and_ledger_narrative_accurately(): void
    {
        // 1. Pengadaan item Dus (1 Dus = 12 Pcs) sebanyak 3 Dus langsung diterima
        $payload = [
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'status' => 'received',
            'items' => [
                [
                    'item_id' => $this->itemDus->id,
                    'quantity' => 3, // 3 Dus
                    'unit' => 'Dus',
                    'conversion_factor' => 12,
                    'unit_price' => 60000, // Rp 60.000 / Dus (Netto Rp 5.000 / Pcs)
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)
            ->post(route('inventory.procurement.store'), $payload);

        $response->assertRedirect(route('inventory.procurement.index', ['tab' => 'procurement']));

        // Stok awal 10 + (3 Dus * 12 Pcs) = 10 + 36 = 46 Pcs
        $this->assertEquals(46, $this->itemDus->fresh()->current_stock);

        // Periksa StockLedger IN memuat narasi konversi lengkap
        $ledger = StockLedger::where('item_id', $this->itemDus->id)
            ->where('transaction_type', 'IN')
            ->latest('id')
            ->first();

        $this->assertNotNull($ledger);
        $this->assertEquals(36, $ledger->quantity);
        $this->assertEquals(10, $ledger->balance_before);
        $this->assertEquals(46, $ledger->balance_after);
        $this->assertStringContainsString('3 Dus @ 12 Pcs/Dus', $ledger->description);
    }

    public function test_procurement_show_json_returns_conversion_details(): void
    {
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-0005',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'ordered',
            'total_amount' => 180000,
            'user_id' => $this->officer->id,
        ]);

        $procurement->details()->create([
            'item_id' => $this->itemDus->id,
            'item_name' => $this->itemDus->name,
            'item_code' => $this->itemDus->code,
            'quantity' => 3,
            'unit' => 'Dus',
            'conversion_factor' => 12,
            'base_quantity' => 36,
            'unit_price' => 60000,
            'subtotal' => 180000,
        ]);

        $response = $this->actingAs($this->officer)
            ->getJson(route('inventory.procurement.show', $procurement));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.details.0.unit', 'Dus');
        $response->assertJsonPath('data.details.0.conversion_factor', 12);
        $response->assertJsonPath('data.details.0.base_quantity', 36);
    }
}
