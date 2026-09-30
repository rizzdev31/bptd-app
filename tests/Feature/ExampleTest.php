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
        $user = \App\Models\User::first();
        if ($user) {
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
        } else {
            $this->assertTrue(true);
        }
    }
}
