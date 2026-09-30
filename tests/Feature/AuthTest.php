<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'petugas',
            'label' => 'Petugas ATK',
            'description' => 'Petugas Pengelola ATK',
        ]);

        $unit = WorkUnit::create([
            'code' => 'TU',
            'name' => 'Subbagian Tata Usaha',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Ahmad Petugas',
            'username' => 'petugas1',
            'nip' => '199203152018011002',
            'email' => 'petugas1@bptd-jatim.go.id',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'work_unit_id' => $unit->id,
            'status' => 'active',
        ]);
    }

    public function test_user_can_login_using_username(): void
    {
        $response = $this->post('/login', [
            'username' => 'petugas1',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_user_can_login_using_nip(): void
    {
        $response = $this->post('/login', [
            'username' => '199203152018011002',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'petugas1',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->user->update(['status' => 'inactive']);

        $response = $this->from('/login')->post('/login', [
            'username' => 'petugas1',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');

        $response->assertRedirect('/login');
        $response->assertSessionHas('show_logout_preloader', true);
        $response->assertSessionHas('status', 'Anda telah berhasil keluar dari sistem.');
        $this->assertGuest();
    }

    public function test_successful_login_flashes_dashboard_preloader(): void
    {
        $response = $this->post('/login', [
            'username' => 'petugas1',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('show_dashboard_preloader', true);
    }
}
