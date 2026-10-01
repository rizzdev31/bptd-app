<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Recipient;
use App\Models\Role;
use App\Models\StockLedger;
use App\Models\StockOut;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StockOutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Recipient $recipient;
    protected Item $itemA;
    protected Item $itemB;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'petugas',
            'label' => 'Petugas ATK',
            'description' => 'Petugas Pengelola ATK',
        ]);

        $workUnit = WorkUnit::create([
            'code' => 'TU',
            'name' => 'Subbagian Tata Usaha',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Petugas Logistik BPTD',
            'username' => 'petugas_atk',
            'nip' => '199203152018011002',
            'email' => 'petugas@bptd-jatim.go.id',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'work_unit_id' => $workUnit->id,
            'status' => 'active',
        ]);

        $this->recipient = Recipient::create([
            'nip' => '198501012010121001',
            'name' => 'Ahmad Fauzi, S.T.',
            'work_unit_id' => $workUnit->id,
            'position' => 'Pengatur Lalu Lintas',
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

        $this->itemA = Item::create([
            'code' => 'ATK-2026-0001',
            'barcode' => '899123456789',
            'name' => 'Pulpen Gel Hitam 0.5mm',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Pcs',
            'minimum_stock' => 10,
            'target_stock' => 50,
            'current_stock' => 40,
            'status' => 'active',
        ]);

        $this->itemB = Item::create([
            'code' => 'ATK-2026-0002',
            'barcode' => '899123456790',
            'name' => 'Kertas HVS A4 80gr',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Rim',
            'minimum_stock' => 5,
            'target_stock' => 30,
            'current_stock' => 20,
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_stock_out_page(): void
    {
        $response = $this->get(route('inventory.stock-out.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_officer_can_view_stock_out_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('inventory.stock-out.index'));

        $response->assertOk();
        $response->assertViewIs('inventory.stock_out.index');
        $response->assertSee('Kasir POS Distribusi');
        $response->assertSee('Pulpen Gel Hitam 0.5mm');
        $response->assertSee('Ahmad Fauzi, S.T.');
    }

    public function test_officer_can_execute_stock_out_transaction_successfully(): void
    {
        $payload = [
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'notes' => 'Operasional pelayanan harian BPTD Jatim',
            'items' => [
                [
                    'item_id' => $this->itemA->id,
                    'quantity' => 5,
                    'notes' => '5 Pcs untuk loket',
                ],
                [
                    'item_id' => $this->itemB->id,
                    'quantity' => 2,
                    'notes' => '2 Rim untuk cetak berita acara',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('inventory.stock-out.store'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        // Verifikasi StockOut header
        $this->assertDatabaseHas('stock_outs', [
            'recipient_name' => 'Ahmad Fauzi, S.T.',
            'recipient_nip' => '198501012010121001',
            'recipient_unit' => 'Subbagian Tata Usaha',
            'user_id' => $this->user->id,
            'total_items' => 2,
            'total_quantity' => 7,
            'status' => 'completed',
        ]);

        $stockOut = StockOut::where('recipient_name', 'Ahmad Fauzi, S.T.')->first();
        $this->assertNotNull($stockOut);
        $this->assertStringStartsWith('OUT-', $stockOut->transaction_number);

        // Verifikasi pengurangan current_stock
        $this->assertEquals(35, $this->itemA->fresh()->current_stock);
        $this->assertEquals(18, $this->itemB->fresh()->current_stock);

        // Verifikasi rincian StockOutDetail
        $this->assertDatabaseHas('stock_out_details', [
            'stock_out_id' => $stockOut->id,
            'item_id' => $this->itemA->id,
            'quantity' => 5,
            'current_stock_before' => 40,
            'current_stock_after' => 35,
        ]);

        $this->assertDatabaseHas('stock_out_details', [
            'stock_out_id' => $stockOut->id,
            'item_id' => $this->itemB->id,
            'quantity' => 2,
            'current_stock_before' => 20,
            'current_stock_after' => 18,
        ]);

        // Verifikasi pencatatan StockLedger
        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->itemA->id,
            'transaction_type' => 'OUT',
            'quantity' => 5,
            'balance_before' => 40,
            'balance_after' => 35,
            'reference_id' => $stockOut->id,
        ]);

        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->itemB->id,
            'transaction_type' => 'OUT',
            'quantity' => 2,
            'balance_before' => 20,
            'balance_after' => 18,
            'reference_id' => $stockOut->id,
        ]);
    }

    public function test_stock_out_fails_when_requested_quantity_exceeds_available_stock(): void
    {
        $payload = [
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'items' => [
                [
                    'item_id' => $this->itemA->id,
                    'quantity' => 100, // Melebihi stok yang hanya 40
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('inventory.stock-out.store'), $payload);

        $response->assertStatus(422);

        // Pastikan stok fisik tidak berubah sama sekali (atomic rollback)
        $this->assertEquals(40, $this->itemA->fresh()->current_stock);
        $this->assertDatabaseCount('stock_outs', 0);
        $this->assertDatabaseCount('stock_ledgers', 0);
    }

    public function test_stock_out_fails_when_item_is_inactive(): void
    {
        $this->itemA->update(['status' => 'inactive']);

        $payload = [
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'items' => [
                [
                    'item_id' => $this->itemA->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('inventory.stock-out.store'), $payload);

        $response->assertStatus(422);
        $this->assertEquals(40, $this->itemA->fresh()->current_stock);
    }

    public function test_officer_can_view_stock_out_detail_json(): void
    {
        $stockOut = StockOut::create([
            'transaction_number' => 'OUT-20261001-0001',
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'recipient_nip' => $this->recipient->nip,
            'recipient_unit' => 'Subbag Tata Usaha',
            'user_id' => $this->user->id,
            'total_items' => 1,
            'total_quantity' => 2,
            'status' => 'completed',
        ]);

        $stockOut->details()->create([
            'item_id' => $this->itemA->id,
            'item_code' => $this->itemA->code,
            'item_name' => $this->itemA->name,
            'quantity' => 2,
            'unit' => 'Pcs',
            'current_stock_before' => 40,
            'current_stock_after' => 38,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('inventory.stock-out.show', $stockOut));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'transaction_number' => 'OUT-20261001-0001',
                'recipient_name' => 'Ahmad Fauzi, S.T.',
            ],
        ]);
    }

    public function test_officer_can_view_printable_sbpb_document(): void
    {
        $stockOut = StockOut::create([
            'transaction_number' => 'OUT-20261001-0001',
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'recipient_nip' => $this->recipient->nip,
            'recipient_unit' => 'Subbag Tata Usaha',
            'user_id' => $this->user->id,
            'total_items' => 1,
            'total_quantity' => 2,
            'status' => 'completed',
        ]);

        $stockOut->details()->create([
            'item_id' => $this->itemA->id,
            'item_code' => $this->itemA->code,
            'item_name' => $this->itemA->name,
            'quantity' => 2,
            'unit' => 'Pcs',
            'current_stock_before' => 40,
            'current_stock_after' => 38,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('inventory.stock-out.print', $stockOut));

        $response->assertOk();
        $response->assertViewIs('inventory.stock_out.print');
        $response->assertSee('SURAT BUKTI PENGELUARAN BARANG (SBPB)');
        $response->assertSee('OUT-20261001-0001');
        $response->assertSee('Ahmad Fauzi, S.T.');
        $response->assertSee('Pulpen Gel Hitam 0.5mm');
    }

    public function test_officer_can_request_item_in_pieces_when_item_is_stored_in_dozens(): void
    {
        $category = Category::where('code', 'ATK')->first();
        $unit = Unit::where('code', 'PCS')->first();

        // Buat Pulpen Pilot dalam satuan Lusin (1 Lusin = 12 Pcs), stok 3 Lusin = 36 Pcs
        $pulpenLusin = Item::create([
            'code' => 'ATK-PILOT-01',
            'name' => 'Pulpen Pilot G2 0.7mm',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Lusin',
            'small_unit' => 'Pcs',
            'conversion_rate' => 12,
            'minimum_stock' => 12,
            'target_stock' => 60,
            'current_stock' => 36, // 3 Lusin
            'status' => 'active',
        ]);

        $payload = [
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'notes' => 'Permintaan 2 pcs pulpen eceran untuk staf loket',
            'items' => [
                [
                    'item_id' => $pulpenLusin->id,
                    'quantity' => 2,
                    'unit' => 'Pcs', // Ambil satuan eceran
                    'notes' => '2 pcs eceran',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('inventory.stock-out.store'), $payload);

        $response->assertOk();

        // Stok fisik berkurang tepat 2 pcs (36 - 2 = 34 pcs)
        $this->assertEquals(34, $pulpenLusin->fresh()->current_stock);

        $this->assertDatabaseHas('stock_out_details', [
            'item_id' => $pulpenLusin->id,
            'quantity' => 2,
            'unit' => 'Pcs',
            'conversion_factor' => 1,
            'base_quantity' => 2,
            'current_stock_before' => 36,
            'current_stock_after' => 34,
        ]);

        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $pulpenLusin->id,
            'transaction_type' => 'OUT',
            'quantity' => 2,
            'balance_before' => 36,
            'balance_after' => 34,
        ]);
    }

    public function test_officer_can_request_item_in_full_dozen_and_deducts_converted_pieces(): void
    {
        $category = Category::where('code', 'ATK')->first();
        $unit = Unit::where('code', 'PCS')->first();

        // Buat Pulpen Pilot dalam satuan Lusin (1 Lusin = 12 Pcs), stok 36 Pcs (3 Lusin)
        $pulpenLusin = Item::create([
            'code' => 'ATK-PILOT-02',
            'name' => 'Pulpen Pilot G2 Biru',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Lusin',
            'small_unit' => 'Pcs',
            'conversion_rate' => 12,
            'minimum_stock' => 12,
            'target_stock' => 60,
            'current_stock' => 36, // 3 Lusin
            'status' => 'active',
        ]);

        $payload = [
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_id' => $this->recipient->id,
            'notes' => 'Permintaan 1 lusin untuk rapat eselon',
            'items' => [
                [
                    'item_id' => $pulpenLusin->id,
                    'quantity' => 1,
                    'unit' => 'Lusin', // Ambil 1 kemasan lusin penuh
                    'notes' => '1 lusin rapat',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('inventory.stock-out.store'), $payload);

        $response->assertOk();

        // Stok fisik berkurang 12 pcs (36 - 12 = 24 pcs = 2 Lusin)
        $this->assertEquals(24, $pulpenLusin->fresh()->current_stock);

        $this->assertDatabaseHas('stock_out_details', [
            'item_id' => $pulpenLusin->id,
            'quantity' => 1,
            'unit' => 'Lusin',
            'conversion_factor' => 12,
            'base_quantity' => 12,
            'current_stock_before' => 36,
            'current_stock_after' => 24,
        ]);

        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $pulpenLusin->id,
            'transaction_type' => 'OUT',
            'quantity' => 12,
            'balance_before' => 36,
            'balance_after' => 24,
        ]);
    }
}
