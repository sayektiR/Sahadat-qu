<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Student;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBranchBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat user leader untuk autentikasi.
     */
    private function loginAsLeader(): User
    {
        $user = User::create([
            'name' => 'Leader Test',
            'email' => 'leader@test.com',
            'password' => bcrypt('password'),
            'role' => 'leader',
        ]);

        $this->actingAs($user);

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-01
    | Tambah cabang - semua data valid
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_01_store_branch_all_valid_data_succeeds(): void
    {
        $this->loginAsLeader();

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'name' => 'Cabang Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-02
    | Nama cabang kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_02_store_branch_empty_name_fails(): void
    {
        $this->loginAsLeader();

        $response = $this->post(route('leader.branches.store'), [
            'name' => '',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('branches', [
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-03
    | Nama cabang sudah digunakan
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_03_store_branch_duplicate_name_fails(): void
    {
        $this->loginAsLeader();

        Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat A',
            'phone' => '081111111111',
        ]);

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang A',
            'address' => 'Alamat B',
            'phone' => '082222222222',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-04
    | Alamat kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_04_store_branch_empty_address_fails(): void
    {
        $this->loginAsLeader();

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang Baru',
            'address' => null,
            'phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors('address');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-05
    | Nomor telepon kosong
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_05_store_branch_empty_phone_fails(): void
    {
        $this->loginAsLeader();

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => null,
        ]);

        $response->assertSessionHasErrors('phone');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-06
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_06_store_branch_name_255_characters_succeeds(): void
    {
        $this->loginAsLeader();

        $name = str_repeat('A', 255);

        $response = $this->post(route('leader.branches.store'), [
            'name' => $name,
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'name' => $name,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-07
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_07_store_branch_name_254_characters_succeeds(): void
    {
        $this->loginAsLeader();

        $name = str_repeat('A', 254);

        $response = $this->post(route('leader.branches.store'), [
            'name' => $name,
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'name' => $name,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-08
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_08_store_branch_name_256_characters_fails(): void
    {
        $this->loginAsLeader();

        $name = str_repeat('A', 256);

        $response = $this->post(route('leader.branches.store'), [
            'name' => $name,
            'address' => 'Jl. Contoh No. 1',
            'phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-09
    | Nomor telepon 20 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_09_store_branch_phone_20_characters_succeeds(): void
    {
        $this->loginAsLeader();

        $phone = str_repeat('1', 20);

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => $phone,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'phone' => $phone,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-10
    | Nomor telepon 19 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_10_store_branch_phone_19_characters_succeeds(): void
    {
        $this->loginAsLeader();

        $phone = str_repeat('1', 19);

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => $phone,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'phone' => $phone,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-11
    | Nomor telepon 21 karakter
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_11_store_branch_phone_21_characters_fails(): void
    {
        $this->loginAsLeader();

        $phone = str_repeat('1', 21);

        $response = $this->post(route('leader.branches.store'), [
            'name' => 'Cabang Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => $phone,
        ]);

        $response->assertSessionHasErrors('phone');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-12
    | Edit seluruh data cabang
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_12_update_branch_all_data_succeeds(): void
    {
        $this->loginAsLeader();

        $branch = Branch::create([
            'name' => 'Cabang Lama',
            'address' => 'Alamat Lama',
            'phone' => '081111111111',
        ]);

        $response = $this->put(
            route('leader.branches.update', $branch),
            [
                'name' => 'Cabang Baru',
                'address' => 'Alamat Baru',
                'phone' => '082222222222',
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Cabang Baru',
            'address' => 'Alamat Baru',
            'phone' => '082222222222',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-13
    | Nama cabang dikosongkan saat edit
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_13_update_branch_empty_name_fails(): void
    {
        $this->loginAsLeader();

        $branch = Branch::create([
            'name' => 'Cabang Lama',
            'address' => 'Alamat Lama',
            'phone' => '081111111111',
        ]);

        $response = $this->put(
            route('leader.branches.update', $branch),
            [
                'name' => '',
                'address' => 'Alamat Baru',
                'phone' => '082222222222',
            ]
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Cabang Lama',
            'address' => 'Alamat Lama',
            'phone' => '081111111111',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-14
    | Hapus cabang yang masih memiliki relasi
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_14_destroy_branch_with_relation_fails(): void
    {
        $this->loginAsLeader();

        $branch = Branch::create([
            'name' => 'Cabang Relasi',
            'address' => 'Alamat',
            'phone' => '081111111111',
        ]);

        // Buat group yang terhubung dengan branch
        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok Test',
        ]);

        // Buat student yang terhubung dengan branch dan group
        Student::create([
            'name' => 'Santri Test',
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'status' => 'active',
        ]);

        $response = $this->delete(
            route('leader.branches.destroy', $branch)
        );

        // Sistem harus menolak penghapusan
        $response->assertSessionHasErrors('delete');

        // Cabang harus tetap ada
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-CB-15
    | Hapus cabang yang tidak memiliki relasi
    |--------------------------------------------------------------------------
    */
    public function test_tc_cb_15_destroy_branch_without_relation_succeeds(): void
    {
        $this->loginAsLeader();

        $branch = Branch::create([
            'name' => 'Cabang Kosong',
            'address' => 'Alamat',
            'phone' => '081111111111',
        ]);

        $response = $this->delete(
            route('leader.branches.destroy', $branch)
        );

        $response->assertRedirect();

        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id,
        ]);
    }
}