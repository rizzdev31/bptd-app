<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $superadminRole;
    protected Role $petugasRole;
    protected Permission $perm1;
    protected Permission $perm2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadminRole = Role::create([
            'name' => 'superadmin',
            'label' => 'Superadmin / Petugas Utama',
            'description' => 'Akses penuh seluruh sistem',
        ]);

        $this->petugasRole = Role::create([
            'name' => 'petugas',
            'label' => 'Petugas ATK',
            'description' => 'Petugas operasional stok',
        ]);

        $unit = WorkUnit::create([
            'code' => 'TU',
            'name' => 'Subbagian Tata Usaha',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'nip' => '198501012010121001',
            'email' => 'admin@bptd-jatim.go.id',
            'password' => Hash::make('password123'),
            'role_id' => $this->superadminRole->id,
            'work_unit_id' => $unit->id,
            'status' => 'active',
        ]);

        $this->perm1 = Permission::create([
            'name' => 'inventory.view',
            'label' => 'Melihat Master ATK & Stok',
            'module' => 'inventory',
        ]);

        $this->perm2 = Permission::create([
            'name' => 'stock_out.create',
            'label' => 'Mencatat Pengeluaran ATK',
            'module' => 'transaksi',
        ]);

        $this->superadminRole->permissions()->sync([$this->perm1->id, $this->perm2->id]);
    }

    public function test_guest_cannot_access_role_settings(): void
    {
        $response = $this->get('/settings/roles');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_role_settings(): void
    {
        $response = $this->actingAs($this->admin)->get('/settings/roles');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Peran &amp; Hak Akses', false);
        $response->assertSee('Petugas ATK');
        $response->assertSee('Melihat Master ATK & Stok');
    }

    public function test_can_store_new_custom_role(): void
    {
        $response = $this->actingAs($this->admin)->post('/settings/roles', [
            'name' => 'auditor_internal',
            'label' => 'Auditor Internal Balai',
            'description' => 'Memantau log audit persediaan barang',
            'permissions' => [$this->perm1->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('roles', [
            'name' => 'auditor_internal',
            'label' => 'Auditor Internal Balai',
        ]);

        $role = Role::where('name', 'auditor_internal')->first();
        $this->assertTrue($role->permissions->contains($this->perm1->id));
    }

    public function test_store_validation_fails_on_duplicate_role_name(): void
    {
        $response = $this->actingAs($this->admin)->post('/settings/roles', [
            'name' => 'superadmin',
            'label' => 'Duplikat Superadmin',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_update_role_info(): void
    {
        $response = $this->actingAs($this->admin)->put("/settings/roles/{$this->petugasRole->id}", [
            'label' => 'Petugas Pengelola ATK & Distribusi',
            'description' => 'Mengelola mutasi stok masuk dan keluar',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('roles', [
            'id' => $this->petugasRole->id,
            'label' => 'Petugas Pengelola ATK & Distribusi',
        ]);
    }

    public function test_can_update_role_permissions_matrix(): void
    {
        $response = $this->actingAs($this->admin)->put("/settings/roles/{$this->petugasRole->id}/permissions", [
            'permissions' => [$this->perm1->id, $this->perm2->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->petugasRole->refresh();
        $this->assertTrue($this->petugasRole->permissions->contains($this->perm1->id));
        $this->assertTrue($this->petugasRole->permissions->contains($this->perm2->id));
    }

    public function test_cannot_delete_protected_system_roles(): void
    {
        $response = $this->actingAs($this->admin)->delete("/settings/roles/{$this->superadminRole->id}");

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseHas('roles', ['id' => $this->superadminRole->id]);
    }

    public function test_can_delete_unused_custom_role(): void
    {
        $customRole = Role::create([
            'name' => 'magang_helper',
            'label' => 'Staf Magang Helper',
            'description' => 'Membantu penghitungan fisik',
        ]);

        $response = $this->actingAs($this->admin)->delete("/settings/roles/{$customRole->id}");

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('roles', ['id' => $customRole->id]);
    }
}
