<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-01
     * Path : 1-2-3-5
     * Pengguna yang sudah login membuka halaman login.
     */
    public function test_authenticated_user_redirected_to_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('login'));

        $response->assertRedirect($user->dashboardRoute());
    }

    /**
     * TC-02
     * Path : 1-2-4-5
     * Guest membuka halaman login.
     */
    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);

        $response->assertViewIs('auth.login');
    }

    /**
     * TC-03
     * Path : 1-2-3-5-6-7
     * Login berhasil.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect($user->dashboardRoute());
    }

    /**
     * TC-04
     * Path : 1-2-3-4-7
     * Login gagal.
     */
    public function test_login_failed(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password_salah',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}