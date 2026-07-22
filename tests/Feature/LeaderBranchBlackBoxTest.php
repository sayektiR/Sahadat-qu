<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LeaderBranchBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat akun leader untuk melewati middleware role:leader.
     */
    private function actingAsLeader(): User
    {
        $leader = User::create([
            'name' => 'Ketua Lembaga',
            'email' => 'leader@example.com',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'role' => 'leader',
            'branch_id' => null,
        ]);

        $this->actingAs($leader);

        return $leader;
    }

    /**
     * Membuat data cabang tanpa menggunakan factory.
     */
    private function createBranch(): Branch
    {
        return Branch::create([
            'name' => 'Cabang Utama',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);
    }

    /**
     * Data admin valid.
     */
    private function validAdminData(Branch $branch): array
    {
        return [
            'name' => 'Admin Cabang',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'branch_id' => $branch->id,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-01
    | Tambah Admin - Semua data valid
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_01_store_admin_all_valid_data_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect(
            route('leader.admins')
        );

        $this->assertDatabaseHas('users', [
            'name' => 'Admin Cabang',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-02
    | Nama lengkap kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_02_store_admin_empty_name_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['name'] = '';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('users', [
            'email' => 'admin@example.com',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-03
    | Email kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_03_store_admin_empty_email_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['email'] = '';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-04
    | Nomor telepon kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_04_store_admin_empty_phone_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['phone'] = null;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'phone' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-05
    | Format email salah
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_05_store_admin_invalid_email_format_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['email'] = 'walisantri.gmail.com';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-06
    | Cabang kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_06_store_admin_empty_branch_fails(): void
    {
        $this->actingAsLeader();

        $data = $this->validAdminData(
            $this->createBranch()
        );

        $data['branch_id'] = null;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('branch_id');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-07
    | Password kosong
    |
    | Catatan:
    | Controller menggunakan required|min:8.
    | Jadi password kosong HARUS gagal.
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_07_store_admin_empty_password_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['password'] = '';
        $data['password_confirmation'] = '';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('password');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-08
    | Konfirmasi password kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_08_store_admin_empty_password_confirmation_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['password_confirmation'] = '';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('password');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-09
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_09_store_admin_name_254_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $name = str_repeat('A', 254);

        $data = $this->validAdminData($branch);
        $data['name'] = $name;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => $name,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-10
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_10_store_admin_name_255_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $name = str_repeat('A', 255);

        $data = $this->validAdminData($branch);
        $data['name'] = $name;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => $name,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-11
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_11_store_admin_name_256_characters_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['name'] = str_repeat('A', 256);

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-12
    | Nomor telepon 20 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_12_store_admin_phone_20_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $phone = str_repeat('1', 20);

        $data = $this->validAdminData($branch);
        $data['phone'] = $phone;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'phone' => $phone,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-13
    | Nomor telepon 19 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_13_store_admin_phone_19_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $phone = str_repeat('1', 19);

        $data = $this->validAdminData($branch);
        $data['phone'] = $phone;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'phone' => $phone,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-14
    | Nomor telepon 21 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_14_store_admin_phone_21_characters_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['phone'] = str_repeat('1', 21);

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('phone');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-15
    | Email 255 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_15_store_admin_email_255_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        // Total 255 karakter dan tetap merupakan email valid.
        $email = str_repeat('a', 243) . '@b.co';

        $data = $this->validAdminData($branch);
        $data['email'] = $email;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-16
    | Hubungan 256 karakter
    |
    | Controller tidak memiliki field hubungan.
    | Diganti menjadi password 256 karakter karena field ini
    | memang tersedia di AdminController.
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_16_store_admin_password_256_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $password = str_repeat('A', 256);

        $data = $this->validAdminData($branch);
        $data['password'] = $password;
        $data['password_confirmation'] = $password;

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-17
    | Password 8 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_17_store_admin_password_8_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['password'] = '12345678';
        $data['password_confirmation'] = '12345678';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-18
    | Password lebih dari 8 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_18_store_admin_password_more_than_8_characters_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['password'] = 'password123';
        $data['password_confirmation'] = 'password123';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertRedirect();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-19
    | Password 7 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_19_store_admin_password_7_characters_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $data = $this->validAdminData($branch);
        $data['password'] = '1234567';
        $data['password_confirmation'] = '1234567';

        $response = $this->post(
            route('leader.admins.store'),
            $data
        );

        $response->assertSessionHasErrors('password');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-20
    | Edit seluruh data admin
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_20_update_admin_all_data_succeeds(): void
    {
        $this->actingAsLeader();

        $branchOld = $this->createBranch();

        $branchNew = Branch::create([
            'name' => 'Cabang Baru',
            'address' => 'Alamat Baru',
            'phone' => '082222222222',
        ]);

        $admin = User::create([
            'name' => 'Admin Lama',
            'email' => 'adminlama@example.com',
            'password' => Hash::make('password123'),
            'phone' => '081111111111',
            'branch_id' => $branchOld->id,
            'role' => 'admin',
        ]);

        $response = $this->put(
            route('leader.admins.update', $admin),
            [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@example.com',
                'password' => 'passwordbaru',
                'password_confirmation' => 'passwordbaru',
                'phone' => '082222222222',
                'branch_id' => $branchNew->id,
            ]
        );

        $response->assertRedirect(
            route('leader.admins')
        );

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baru',
            'email' => 'adminbaru@example.com',
            'phone' => '082222222222',
            'branch_id' => $branchNew->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-21
    | Edit sebagian data admin
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_21_update_admin_some_data_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $admin = User::create([
            'name' => 'Admin Lama',
            'email' => 'adminlama@example.com',
            'password' => Hash::make('password123'),
            'phone' => '081111111111',
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $response = $this->put(
            route('leader.admins.update', $admin),
            [
                'name' => 'Admin Diperbarui',
                'email' => 'adminlama@example.com',
                'password' => '',
                'password_confirmation' => '',
                'phone' => '081111111111',
                'branch_id' => $branch->id,
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Diperbarui',
            'email' => 'adminlama@example.com',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-22
    | Nama dan email kosong saat edit
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_22_update_admin_empty_name_and_email_fails(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $admin = User::create([
            'name' => 'Admin Lama',
            'email' => 'adminlama@example.com',
            'password' => Hash::make('password123'),
            'phone' => '081111111111',
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $response = $this->put(
            route('leader.admins.update', $admin),
            [
                'name' => '',
                'email' => '',
                'password' => '',
                'password_confirmation' => '',
                'phone' => '081111111111',
                'branch_id' => $branch->id,
            ]
        );

        $response->assertSessionHasErrors([
            'name',
            'email',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Lama',
            'email' => 'adminlama@example.com',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-ACB-23
    | Hapus data admin
    |--------------------------------------------------------------------------
    */
    public function test_tc_acb_23_destroy_admin_succeeds(): void
    {
        $this->actingAsLeader();

        $branch = $this->createBranch();

        $admin = User::create([
            'name' => 'Admin Hapus',
            'email' => 'adminhapus@example.com',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $response = $this->delete(
            route('leader.admins.destroy', $admin)
        );

        $response->assertRedirect(
            route('leader.admins')
        );

        $this->assertDatabaseMissing('users', [
            'id' => $admin->id,
        ]);
    }
}