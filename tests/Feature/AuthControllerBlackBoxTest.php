<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    // TC-LG-01
    // EP: Email dan password valid
    public function test_tc_lg_01_login_dengan_email_dan_password_valid()
    {
        $user = User::factory()->create([
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => 'password123',
            'remember' => false,
        ]);

        $response->assertRedirect($user->dashboardRoute());

        $this->assertAuthenticatedAs($user);
    }

    // TC-LG-02
    // EP: Password salah
    public function test_tc_lg_02_login_dengan_password_salah()
    {
        User::factory()->create([
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'email' => 'Email atau password tidak sesuai.',
        ]);

        $this->assertGuest();
    }

    // TC-LG-03
    // EP: Email tidak terdaftar
    public function test_tc_lg_03_login_dengan_email_tidak_terdaftar()
    {
        $response = $this->post(route('login.store'), [
            'email' => 'admin.jakarta@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'email' => 'Email atau password tidak sesuai.',
        ]);

        $this->assertGuest();
    }

    // TC-LG-04
    // BVA: Email kosong
    public function test_tc_lg_04_login_dengan_email_kosong()
    {
        $response = $this->post(route('login.store'), [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // TC-LG-05
    // BVA: Password kosong
    public function test_tc_lg_05_login_dengan_password_kosong()
    {
        $response = $this->post(route('login.store'), [
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    // TC-LG-06
    // EP: Format email tidak valid
    public function test_tc_lg_06_login_dengan_format_email_tidak_valid()
    {
        $response = $this->post(route('login.store'), [
            'email' => 'admingmail.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // TC-LG-07
    // EP: Akun tidak aktif
    public function test_tc_lg_07_login_dengan_akun_tidak_aktif()
    {
        User::factory()->create([
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => false,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'email' => 'Email atau password tidak sesuai.',
        ]);

        $this->assertGuest();
    }

    // TC-LG-08
    // EP: Remember me dicentang
    // TC-LG-08
    // EP: Remember me dicentang
    public function test_tc_lg_08_login_dengan_remember_me()
    {
        $user = User::factory()->create([
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertRedirect($user->dashboardRoute());

        $this->assertAuthenticatedAs($user);
    }

    // TC-LG-09
    // EP: Remember me tidak dicentang
    // TC-LG-09
    // EP: Remember me tidak dicentang
    public function test_tc_lg_09_login_tanpa_remember_me()
    {
        $user = User::factory()->create([
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin.bojonegoro@gmail.com',
            'password' => 'password123',
            'remember' => false,
        ]);

        $response->assertRedirect($user->dashboardRoute());

        $this->assertAuthenticatedAs($user);
    }

    // TC-LG-10
    // EP: User belum login
    public function test_tc_lg_10_guest_dapat_melihat_halaman_login()
    {
        $response = $this->get(route('login'));

        $response->assertOk();

        $response->assertViewIs('auth.login');
    }

    // TC-LG-11
    // EP: User sudah login
    public function test_tc_lg_11_user_yang_sudah_login_diarahkan_ke_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('login'));

        $response->assertRedirect(
            $user->dashboardRoute()
        );
    }

    // TC-LG-12
    // EP: User melakukan logout
    public function test_tc_lg_12_user_dapat_melakukan_logout()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect(
            route('login')
        );

        $this->assertGuest();
    }
}