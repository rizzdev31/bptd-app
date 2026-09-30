<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Unit $unit;
    protected Supplier $supplier;

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
            'name' => 'Petugas ATK',
            'username' => 'petugas_atk',
            'nip' => '199203152018011002',
            'email' => 'petugas@bptd-jatim.go.id',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'work_unit_id' => $workUnit->id,
            'status' => 'active',
        ]);

        $this->category = Category::create([
            'code' => 'KAT-01',
            'name' => 'Alat Tulis',
            'status' => 'active',
        ]);

        $this->unit = Unit::create([
            'code' => 'BOX',
            'name' => 'Box',
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'PT Graha Alat Tulis',
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_items_page(): void
    {
        $response = $this->get('/inventory/items');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_items_index(): void
    {
        Item::create([
            'code' => 'ATK-2026-0001',
            'name' => 'Pulpen Standard AE7',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Box',
            'minimum_stock' => 10,
            'target_stock' => 50,
            'current_stock' => 30,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get('/inventory/items');

        $response->assertSee('Pulpen Standard AE7');
        $response->assertSee('ATK-2026-0001');
        $response->assertSee('Katalog dan Master Data ATK');
    }

    public function test_can_filter_items_by_stock_status(): void
    {
        Item::create([
            'code' => 'ATK-001',
            'name' => 'Barang Tersedia',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Box',
            'minimum_stock' => 10,
            'target_stock' => 50,
            'current_stock' => 30, // Tersedia
            'status' => 'active',
        ]);

        Item::create([
            'code' => 'ATK-002',
            'name' => 'Barang Menipis',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Box',
            'minimum_stock' => 15,
            'target_stock' => 50,
            'current_stock' => 5, // Menipis
            'status' => 'active',
        ]);

        Item::create([
            'code' => 'ATK-003',
            'name' => 'Barang Habis',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Box',
            'minimum_stock' => 10,
            'target_stock' => 50,
            'current_stock' => 0, // Habis
            'status' => 'active',
        ]);

        // Filter low_stock
        $responseLow = $this->actingAs($this->user)->get('/inventory/items?stock_status=low_stock');
        $responseLow->assertStatus(200);
        $responseLow->assertSee('Barang Menipis');
        $responseLow->assertDontSee('Barang Tersedia');

        // Filter out_of_stock
        $responseOut = $this->actingAs($this->user)->get('/inventory/items?stock_status=out_of_stock');
        $responseOut->assertStatus(200);
        $responseOut->assertSee('Barang Habis');
        $responseOut->assertDontSee('Barang Tersedia');
    }

    public function test_can_store_new_atk_item(): void
    {
        $payload = [
            'code' => 'ATK-2026-9999',
            'barcode' => '899999999999',
            'name' => 'Kertas Continuous Form 2 Ply',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'minimum_stock' => 5,
            'target_stock' => 20,
            'current_stock' => 12,
            'storage_location' => 'Gudang 01',
            'supplier_id' => $this->supplier->id,
            'status' => 'active',
            'description' => 'Kertas untuk cetak laporan slip gaji dinas',
        ];

        $response = $this->actingAs($this->user)->post('/inventory/items', $payload);

        $response->assertRedirect('/inventory/items');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('items', [
            'code' => 'ATK-2026-9999',
            'name' => 'Kertas Continuous Form 2 Ply',
            'current_stock' => 12,
        ]);
    }

    public function test_store_validation_fails_on_duplicate_code(): void
    {
        Item::create([
            'code' => 'ATK-DUPLIKAT',
            'name' => 'Barang Pertama',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Box',
            'minimum_stock' => 5,
            'target_stock' => 20,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post('/inventory/items', [
            'code' => 'ATK-DUPLIKAT',
            'name' => 'Barang Kedua Duplikat',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'minimum_stock' => 5,
            'target_stock' => 20,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_can_update_atk_item(): void
    {
        $item = Item::create([
            'code' => 'ATK-2026-0050',
            'name' => 'Gunting Sedang',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Pcs',
            'minimum_stock' => 5,
            'target_stock' => 15,
            'current_stock' => 8,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->put("/inventory/items/{$item->id}", [
            'code' => 'ATK-2026-0050',
            'name' => 'Gunting Kantor Stainless Besi',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'minimum_stock' => 10,
            'target_stock' => 30,
            'status' => 'active',
            'storage_location' => 'Laci Meja 3',
        ]);

        $response->assertRedirect('/inventory/items');
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Gunting Kantor Stainless Besi',
            'minimum_stock' => 10,
            'storage_location' => 'Laci Meja 3',
        ]);
    }

    public function test_can_toggle_item_status(): void
    {
        $item = Item::create([
            'code' => 'ATK-TOGGLE',
            'name' => 'Barang Diuji Toggle',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'unit' => 'Pcs',
            'minimum_stock' => 5,
            'target_stock' => 15,
            'current_stock' => 8,
            'status' => 'active',
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->user)->delete("/inventory/items/{$item->id}");

        $response->assertRedirect('/inventory/items');
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'inactive',
        ]);

        // Toggle back to active
        $response2 = $this->actingAs($this->user)->delete("/inventory/items/{$item->id}");
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'active',
        ]);
    }
}
