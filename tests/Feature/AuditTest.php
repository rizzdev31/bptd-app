<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTest extends TestCase
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
            'name' => 'Petugas Audit BPTD',
            'username' => 'petugas_audit',
            'nip' => '199501012019011001',
            'email' => 'audit@bptd-jatim.go.id',
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

        $this->workUnit = WorkUnit::create([
            'code' => 'LLJ',
            'name' => 'Seksi Lalu Lintas Jalan',
            'status' => 'active',
        ]);

        $this->recipient = Recipient::create([
            'name' => 'Ahmad Fauzi, S.T.',
            'nip' => '198708152011011002',
            'work_unit_id' => $this->workUnit->id,
            'position' => 'Pemeriksa Transportasi',
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-AUDIT',
            'name' => 'CV Sumber Makmur',
            'status' => 'active',
        ]);

        $this->item = Item::create([
            'code' => 'ATK-KRT-02',
            'name' => 'Kertas HVS Folio F4 70gr Sinar Dunia',
            'category_id' => $category->id,
            'unit_id' => $unitPcs->id,
            'unit' => 'Rim',
            'small_unit' => 'Lembar',
            'conversion_rate' => 500,
            'minimum_stock' => 10,
            'target_stock' => 100,
            'current_stock' => 5000,
            'storage_location' => 'Rak Arsip B2',
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_audit(): void
    {
        $response = $this->get(route('inventory.audit.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_officer_can_view_audit_index_table(): void
    {
        // Create sample audit log
        AuditLog::record(
            action: 'CREATE',
            module: 'Master ATK',
            description: 'Penambahan item tes audit',
            recordType: Item::class,
            recordId: $this->item->id
        );

        $response = $this->actingAs($this->officer)
            ->get(route('inventory.audit.index'));

        $response->assertStatus(200);
        $response->assertSee('Audit Trail &amp; Log Riwayat Aktivitas', false);
        $response->assertSee('Penambahan item tes audit');
        $response->assertSee('Master ATK');
    }

    public function test_officer_can_view_audit_timeline_view(): void
    {
        AuditLog::record(
            action: 'LOGIN',
            module: 'Autentikasi',
            description: 'User login berhasil',
            recordType: User::class,
            recordId: $this->officer->id
        );

        $response = $this->actingAs($this->officer)
            ->get(route('inventory.audit.index', ['view' => 'timeline']));

        $response->assertStatus(200);
        $response->assertSee('Feed Kronologis Aktivitas');
        $response->assertSee('User login berhasil');
    }

    public function test_officer_can_filter_audit_logs(): void
    {
        AuditLog::record(
            action: 'STOCK_OUT',
            module: 'Permintaan ATK',
            description: 'Pengeluaran Kertas HVS',
            recordType: Item::class,
            recordId: $this->item->id
        );

        AuditLog::record(
            action: 'CREATE',
            module: 'Pegawai',
            description: 'Penambahan pegawai baru',
            recordType: Recipient::class,
            recordId: $this->recipient->id
        );

        // Filter for Permintaan ATK only
        $response = $this->actingAs($this->officer)
            ->get(route('inventory.audit.index', ['module' => 'Permintaan ATK']));

        $response->assertStatus(200);
        $response->assertSee('Pengeluaran Kertas HVS');
        $response->assertDontSee('Penambahan pegawai baru');
    }

    public function test_stock_out_transaction_creates_audit_log_automatically(): void
    {
        $postData = [
            'transaction_date' => date('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'notes' => 'Keperluan verifikasi audit log',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 2,
                    'unit' => 'Rim',
                    'notes' => '2 Rim',
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)
            ->post(route('inventory.stock-out.store'), $postData);

        $response->assertRedirect();

        // Verify audit log exists
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'STOCK_OUT',
            'module' => 'Permintaan ATK',
            'record_type' => 'StockOut',
        ]);

        $log = AuditLog::where('action', 'STOCK_OUT')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Pengeluaran ATK no. OUT-', $log->description);
        $this->assertStringContainsString($this->recipient->name, $log->description);
    }

    public function test_stock_adjustment_creates_audit_log_automatically(): void
    {
        $postData = [
            'adjustment_date' => date('Y-m-d'),
            'reason' => 'Koreksi fisik selisih audit triwulan',
            'type' => 'CORRECTION',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'mode' => 'actual',
                    'actual_stock' => 45,
                    'notes' => 'Fisik rusak 5',
                ],
            ],
        ];

        $response = $this->actingAs($this->officer)
            ->post(route('inventory.stock-control.adjustment.store'), $postData);

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'ADJUSTMENT',
            'module' => 'Kendali Stok',
            'record_type' => 'StockAdjustment',
        ]);
    }

    public function test_procurement_receive_creates_audit_log_automatically(): void
    {
        $procurement = Procurement::create([
            'procurement_number' => 'PO-20261005-9999',
            'date' => date('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => 'ordered',
            'total_amount' => 500000,
            'user_id' => $this->officer->id,
        ]);

        $procurement->details()->create([
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_code' => $this->item->code,
            'quantity' => 10,
            'unit' => 'Rim',
            'conversion_factor' => 1,
            'base_quantity' => 10,
            'unit_price' => 50000,
            'subtotal' => 500000,
        ]);

        $response = $this->actingAs($this->officer)
            ->post(route('inventory.procurement.receive', $procurement));

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'STOCK_IN',
            'module' => 'Penerimaan Barang',
            'record_type' => 'Procurement',
        ]);
    }

    public function test_officer_can_show_audit_log_json_with_diff(): void
    {
        $log = AuditLog::record(
            action: 'UPDATE',
            module: 'Master ATK',
            description: 'Ubah data barang',
            recordType: Item::class,
            recordId: $this->item->id,
            oldValues: ['current_stock' => 50, 'name' => 'Kertas Lama'],
            newValues: ['current_stock' => 60, 'name' => 'Kertas Baru']
        );

        $response = $this->actingAs($this->officer)
            ->get(route('inventory.audit.show', $log));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'action' => 'UPDATE',
                'module' => 'Master ATK',
                'has_diff' => true,
                'old_values' => ['current_stock' => 50, 'name' => 'Kertas Lama'],
                'new_values' => ['current_stock' => 60, 'name' => 'Kertas Baru'],
            ],
        ]);
    }

    public function test_officer_can_export_audit_to_excel(): void
    {
        AuditLog::record(
            action: 'CREATE',
            module: 'Master ATK',
            description: 'Audit Excel Test Item',
            recordType: Item::class,
            recordId: $this->item->id
        );

        $response = $this->actingAs($this->officer)
            ->get(route('inventory.audit.export-excel'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('PK', $content); // Valid ZIP/OpenXML signature
    }

    public function test_officer_can_export_audit_to_csv(): void
    {
        AuditLog::record(
            action: 'CREATE',
            module: 'Master ATK',
            description: 'Audit CSV Test Item',
            recordType: Item::class,
            recordId: $this->item->id
        );

        $response = $this->actingAs($this->officer)
            ->get(route('inventory.audit.export-csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
    }
}
