<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAccountWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PATH 1
     *
     * Password tidak diubah.
     *
     * Jalur:
     * 1 → 2 → 3 → 4 (False) → 7 → 8 → 9
     */
    public function test_path_1_update_account_without_password()
    {
        $user = User::factory()->create([
            'name' => 'Admin Lama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.account.update'), [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@example.com',
                'phone' => '08123456789',
                'address' => 'Alamat Baru',
            ]);

        $response->assertRedirect(route('admin.settings'));

        $response->assertSessionHas(
            'status',
            'Pengaturan akun berhasil diperbarui.'
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Admin Baru',
            'email' => 'adminbaru@example.com',
            'phone' => '08123456789',
            'address' => 'Alamat Baru',
        ]);
    }

    /**
     * PATH 2
     *
     * Password diubah dan password lama benar.
     *
     * Jalur:
     * 1 → 2 → 3 → 4 (True) → 5 (False) → 7 → 8 → 9
     */
    public function test_path_2_update_account_with_correct_current_password()
    {
        $user = User::factory()->create([
            'name' => 'Admin Lama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.account.update'), [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@example.com',
                'phone' => '08123456789',
                'address' => 'Alamat Baru',

                'current_password' => 'password123',
                'password' => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ]);

        $response->assertRedirect(route('admin.settings'));

        $response->assertSessionHas(
            'status',
            'Pengaturan akun berhasil diperbarui.'
        );

        $user->refresh();

        $this->assertEquals('Admin Baru', $user->name);
        $this->assertEquals('adminbaru@example.com', $user->email);

        $this->assertTrue(
            Hash::check('passwordbaru123', $user->password)
        );
    }

    /**
     * PATH 3
     *
     * Password diubah tetapi password lama salah.
     *
     * Jalur:
     * 1 → 2 → 3 → 4 (True) → 5 (True) → 6
     */
    public function test_path_3_update_account_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'name' => 'Admin Lama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.account.update'), [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@example.com',
                'phone' => '08123456789',
                'address' => 'Alamat Baru',

                'current_password' => 'password_salah',
                'password' => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ]);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'current_password',
        ]);

        $user->refresh();

        // Data tidak berubah karena password lama salah
        $this->assertEquals('Admin Lama', $user->name);
        $this->assertEquals('admin@example.com', $user->email);

        $this->assertTrue(
            Hash::check('password123', $user->password)
        );
    }
}