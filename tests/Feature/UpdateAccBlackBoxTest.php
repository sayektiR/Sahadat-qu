<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateAccBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Test',
        ]);

        $this->user = User::factory()->create([
            'branch_id' => $this->branch->id,
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'phone' => '08123456789',
            'address' => 'Alamat Test',
            'password' => Hash::make('passwordlama'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($this->user);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function validAccountData(array $override = []): array
    {
        return array_merge([
            'name' => 'Admin Test Updated',
            'email' => 'adminupdated@test.com',
            'phone' => '08123456789',
            'address' => 'Alamat Baru',
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ], $override);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-01
    | Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_01_update_account_all_valid_data_succeeds(): void
    {
        $data = $this->validAccountData();

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Admin Test Updated',
            'email' => 'adminupdated@test.com',
            'phone' => '08123456789',
            'address' => 'Alamat Baru',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-02
    | Nama kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_02_update_account_empty_name_fails(): void
    {
        $data = $this->validAccountData([
            'name' => '',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-03
    | Email kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_03_update_account_empty_email_fails(): void
    {
        $data = $this->validAccountData([
            'email' => '',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-04
    | Format email tidak valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_04_update_account_invalid_email_fails(): void
    {
        $data = $this->validAccountData([
            'email' => 'admin.gmail.com',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-05
    | Email sudah digunakan akun lain
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_05_update_account_duplicate_email_fails(): void
    {
        User::factory()->create([
            'branch_id' => $this->branch->id,
            'email' => 'used@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $data = $this->validAccountData([
            'email' => 'used@test.com',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-06
    | Password baru diisi tetapi password lama kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_06_new_password_without_current_password_fails(): void
    {
        $data = $this->validAccountData([
            'current_password' => '',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('current_password');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-07
    | Password lama salah
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_07_wrong_current_password_fails(): void
    {
        $data = $this->validAccountData([
            'current_password' => 'passwordsalah',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors(
            'current_password',
            'Password lama tidak sesuai.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-08
    | Password baru dan konfirmasi berbeda
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_08_password_confirmation_does_not_match_fails(): void
    {
        $data = $this->validAccountData([
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordberbeda',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('password');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-09
    | Password baru valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_09_valid_new_password_succeeds(): void
    {
        $data = $this->validAccountData([
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));

        $this->user->refresh();

        $this->assertTrue(
            Hash::check('passwordbaru', $this->user->password)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-10
    | Nomor telepon dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_10_empty_phone_succeeds(): void
    {
        $data = $this->validAccountData([
            'phone' => null,
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'phone' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-11
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_11_name_255_chars_succeeds(): void
    {
        $data = $this->validAccountData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-12
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_12_name_254_chars_succeeds(): void
    {
        $data = $this->validAccountData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-13
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_13_name_256_chars_fails(): void
    {
        $data = $this->validAccountData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-14
    | Nomor telepon 50 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_14_phone_50_chars_succeeds(): void
    {
        $data = $this->validAccountData([
            'phone' => str_repeat('1', 50),
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-15
    | Nomor telepon 49 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_15_phone_49_chars_succeeds(): void
    {
        $data = $this->validAccountData([
            'phone' => str_repeat('1', 49),
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertRedirect(route('admin.settings'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-AKD-16
    | Nomor telepon 51 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_akd_16_phone_51_chars_fails(): void
    {
        $data = $this->validAccountData([
            'phone' => str_repeat('1', 51),
        ]);

        $response = $this->put(
            route('admin.settings.account.update'),
            $data
        );

        $response->assertSessionHasErrors('phone');
    }
}