<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_returns_successful_response(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('BPTD Kelas II Jawa Timur');
    }

    public function test_unauthenticated_dashboard_redirects_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_dashboard_returns_success(): void
    {
        $role = \App\Models\Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $user = \App\Models\User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test_1',
            'email' => 'admin1@bptd.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Data Real Time');
        $response->assertSee('Permintaan & Pengeluaran ATK');
    }

    public function test_dashboard_displays_realtime_stock_out_data(): void
    {
        $role = \App\Models\Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $user = \App\Models\User::create([
            'name' => 'Admin Test 2',
            'username' => 'admin_test_2',
            'email' => 'admin2@bptd.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $category = \App\Models\Category::firstOrCreate(['code' => 'ATK'], ['name' => 'Alat Tulis']);
        $unit = \App\Models\Unit::firstOrCreate(['code' => 'PACK'], ['name' => 'Pack']);

        $item = \App\Models\Item::create([
            'code' => 'ATK-TST-01',
            'name' => 'Kertas Cover Buffalo',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'unit' => 'Pack',
            'minimum_stock' => 5,
            'target_stock' => 50,
            'current_stock' => 40,
            'status' => 'active',
        ]);

        $stockOut = \App\Models\StockOut::create([
            'transaction_number' => 'OUT-202610-9999',
            'transaction_date' => now()->format('Y-m-d'),
            'recipient_name' => 'Bambang Pamungkas',
            'recipient_nip' => '199001012015011002',
            'recipient_unit' => 'Seksi Sarana dan Prasarana',
            'user_id' => $user->id,
            'total_items' => 1,
            'total_quantity' => 10,
            'notes' => 'Permintaan kertas untuk kegiatan sosialisasi',
            'status' => 'completed',
        ]);

        $stockOut->details()->create([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'item_code' => $item->code,
            'quantity' => 10,
            'unit' => 'Pack',
            'conversion_factor' => 1,
            'base_quantity' => 10,
            'current_stock_before' => 50,
            'current_stock_after' => 40,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('OUT-202610-9999');
        $response->assertSee('Bambang Pamungkas');
        $response->assertSee('Seksi Sarana dan Prasarana');
        $response->assertSee('Kertas Cover Buffalo');
    }
}
