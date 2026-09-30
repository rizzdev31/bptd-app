<?php

namespace Tests\Feature;

use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected WorkUnit $unit;
    protected Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create([
            'name' => 'superadmin',
            'label' => 'Superadmin',
            'description' => 'Akses penuh sistem',
        ]);

        $this->unit = WorkUnit::create([
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
            'role_id' => $this->role->id,
            'work_unit_id' => $this->unit->id,
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_employee_page(): void
    {
        $response = $this->get('/pegawai');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_employee_index(): void
    {
        Recipient::create([
            'nip' => '199001012015011001',
            'name' => 'Budi Santoso',
            'position' => 'Analis Anggaran',
            'work_unit_id' => $this->unit->id,
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get('/pegawai');

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('199001012015011001');
    }

    public function test_admin_can_store_new_employee_without_system_account(): void
    {
        $payload = [
            'nip' => '199505052020011005',
            'name' => 'Siti Rahma',
            'position' => 'Pengadministrasi Umum',
            'work_unit_id' => $this->unit->id,
            'phone' => '081298765432',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->post('/pegawai', $payload);

        $response->assertRedirect('/pegawai');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('recipients', [
            'nip' => '199505052020011005',
            'name' => 'Siti Rahma',
        ]);
    }

    public function test_admin_can_store_employee_with_user_account(): void
    {
        $payload = [
            'nip' => '199606062021012002',
            'name' => 'Dewi Anggraini',
            'position' => 'Pranata Komputer',
            'work_unit_id' => $this->unit->id,
            'phone' => '081299887766',
            'status' => 'active',
            'create_user' => '1',
            'username' => 'dewi96',
            'email' => 'dewi@bptd-jatim.go.id',
            'role_id' => $this->role->id,
            'password' => 'password123',
        ];

        $response = $this->actingAs($this->admin)->post('/pegawai', $payload);

        $response->assertRedirect('/pegawai');
        $this->assertDatabaseHas('recipients', [
            'nip' => '199606062021012002',
        ]);
        $this->assertDatabaseHas('users', [
            'nip' => '199606062021012002',
            'username' => 'dewi96',
        ]);
    }

    public function test_admin_can_update_employee(): void
    {
        $recipient = Recipient::create([
            'nip' => '199101012014011003',
            'name' => 'Riko Pratama',
            'position' => 'Staff Operasional',
            'work_unit_id' => $this->unit->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put("/pegawai/{$recipient->id}", [
            'nip' => '199101012014011003',
            'name' => 'Riko Pratama M.Si',
            'position' => 'Kepala Seksi Operasional',
            'work_unit_id' => $this->unit->id,
            'status' => 'active',
        ]);

        $response->assertRedirect('/pegawai');
        $this->assertDatabaseHas('recipients', [
            'id' => $recipient->id,
            'name' => 'Riko Pratama M.Si',
            'position' => 'Kepala Seksi Operasional',
        ]);
    }

    public function test_admin_can_toggle_employee_status(): void
    {
        $recipient = Recipient::create([
            'nip' => '199303032017011004',
            'name' => 'Hendra Kusuma',
            'position' => 'Teknisi Sarpras',
            'work_unit_id' => $this->unit->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/pegawai/{$recipient->id}");

        $response->assertRedirect('/pegawai');
        $this->assertDatabaseHas('recipients', [
            'id' => $recipient->id,
            'status' => 'inactive',
        ]);
    }
}
