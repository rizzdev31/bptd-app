<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private Item $item;
    private WorkUnit $workUnit;
    private Recipient $recipient;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'petugas_atk',
            'label' => 'Petugas ATK',
            'description' => 'Operator Pengelola Persediaan',
        ]);

        $this->officer = User::create([
            'name' => 'Petugas Laporan BPTD',
            'username' => 'petugas_laporan',
            'nip' => '199304122017011003',
            'email' => 'laporan@bptd-jatim.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $category = Category::create([
            'code' => 'KERTAS',
            'name' => 'Kertas & Penggandaan',
            'status' => 'active',
        ]);

        $unitPcs = Unit::create([
            'code' => 'PCS',
            'name' => 'Pcs',
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-002',
            'name' => 'PT Mitra Sarana Surabaya',
            'contact_person' => 'Ibu Maya',
            'status' => 'active',
        ]);

        $this->workUnit = WorkUnit::create([
            'code' => 'LLJ',
            'name' => 'Seksi Lalu Lintas Jalan',
            'description' => 'Seksi LLJ BPTD Jatim',
            'status' => 'active',
        ]);

        $this->recipient = Recipient::create([
            'name' => 'Agus Wicaksono, S.T.',
            'nip' => '198506152010011004',
            'work_unit_id' => $this->workUnit->id,
            'position' => 'Pengawas Keselamatan',
            'status' => 'active',
        ]);

        $this->item = Item::create([
            'code' => 'ATK-KRT-01',
            'name' => 'Kertas HVS A4 75gr PaperOne',
            'category_id' => $category->id,
            'unit_id' => $unitPcs->id,
            'unit' => 'Rim',
            'small_unit' => 'Lembar',
            'conversion_rate' => 500,
            'minimum_stock' => 10,
            'target_stock' => 100,
            'current_stock' => 50,
            'storage_location' => 'Rak Arsip A1',
            'status' => 'active',
        ]);

        // Sample Stock Out
        $stockOut = StockOut::create([
            'transaction_number' => 'OUT-20261005-0001',
            'transaction_date' => date('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'recipient_nip' => $this->recipient->nip,
            'recipient_unit' => $this->workUnit->name,
            'recipient_position' => $this->recipient->position,
            'user_id' => $this->officer->id,
            'total_items' => 1,
            'total_quantity' => 5,
            'notes' => 'Keperluan Rapat Koordinasi Angkutan',
            'status' => 'completed',
        ]);
        $stockOut->details()->create([
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_code' => $this->item->code,
            'quantity' => 5,
            'unit' => 'Rim',
            'conversion_factor' => 1,
            'base_quantity' => 5,
        ]);

        // Sample Stock In
        $stockIn = StockIn::create([
            'transaction_number' => 'IN-20261005-0001',
            'date' => date('Y-m-d'),
            'source' => 'Procurement',
            'supplier_name' => $this->supplier->name,
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->officer->id,
            'total_items' => 1,
            'total_quantity' => 20,
        ]);
        $stockIn->details()->create([
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_code' => $this->item->code,
            'quantity' => 20,
            'unit' => 'Rim',
            'conversion_factor' => 1,
            'base_quantity' => 20,
        ]);

        // Sample Procurement
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-0001',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'received',
            'total_amount' => 1000000,
            'user_id' => $this->officer->id,
        ]);
        $procurement->details()->create([
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_code' => $this->item->code,
            'quantity' => 20,
            'unit' => 'Rim',
            'conversion_factor' => 1,
            'base_quantity' => 20,
            'unit_price' => 50000,
            'subtotal' => 1000000,
        ]);
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->get(route('inventory.reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_officer_can_view_stock_report_tab(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.index', ['type' => 'stock']));

        $response->assertStatus(200);
        $response->assertSee('Laporan &amp; Rekapitulasi Persediaan ATK', false);
        $response->assertSee('Kertas HVS A4 75gr PaperOne');
        $response->assertSee('Rak Arsip A1');
    }

    public function test_officer_can_view_stock_out_report_tab(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.index', ['type' => 'stock_out']));

        $response->assertStatus(200);
        $response->assertSee('OUT-20261005-0001');
        $response->assertSee('Agus Wicaksono, S.T.');
        $response->assertSee('Seksi Lalu Lintas Jalan');
    }

    public function test_officer_can_view_stock_in_report_tab(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.index', ['type' => 'stock_in']));

        $response->assertStatus(200);
        $response->assertSee('IN-20261005-0001');
        $response->assertSee('PT Mitra Sarana Surabaya');
    }

    public function test_officer_can_view_procurement_report_tab(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.index', ['type' => 'procurement']));

        $response->assertStatus(200);
        $response->assertSee('PO-20261005-0001');
        $response->assertSee('PT Mitra Sarana Surabaya');
    }

    public function test_officer_can_view_unit_usage_report_tab(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.index', ['type' => 'unit_usage']));

        $response->assertStatus(200);
        $response->assertSee('Seksi Lalu Lintas Jalan');
        $response->assertSee('5 Pcs');
    }

    public function test_officer_can_export_stock_report_to_excel(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.export-excel', ['type' => 'stock']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('PK', $content); // Valid ZIP/OpenXML signature
    }

    public function test_officer_can_export_stock_out_report_to_excel(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.export-excel', ['type' => 'stock_out']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('PK', $content);
    }

    public function test_officer_can_export_stock_report_to_csv(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.export-csv', ['type' => 'stock']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
    }

    public function test_officer_can_view_report_print_page(): void
    {
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.reports.print', ['type' => 'stock']));

        $response->assertStatus(200);
        $response->assertSee('KEMENTERIAN PERHUBUNGAN');
        $response->assertSee('BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR');
        $response->assertSee('Kertas HVS A4 75gr PaperOne');
    }
}
