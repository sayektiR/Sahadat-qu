<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrancAccBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Lama',
            'address' => 'Alamat Lama',
            'phone' => '08123456789',
            'head_name' => 'Kepala Cabang Lama',
        ]);

        $this->admin = User::factory()->create([
            'branch_id' => $this->branch->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function validBranchData(array $override = []): array
    {
        return array_merge([
            'name' => 'Cabang Baru',
            'address' => 'Alamat Cabang Baru',
            'phone' => '081234567890',
            'head_name' => 'Kepala Cabang Baru',
        ], $override);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-01
    | Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_01_update_branch_all_valid_data_succeeds(): void
    {
        $data = $this->validBranchData();

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $response->assertSessionHas(
            'status',
            'Pengaturan cabang berhasil diperbarui.'
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'name' => 'Cabang Baru',
            'address' => 'Alamat Cabang Baru',
            'phone' => '081234567890',
            'head_name' => 'Kepala Cabang Baru',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-02
    | Nama cabang kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_02_update_branch_empty_name_fails(): void
    {
        $data = $this->validBranchData([
            'name' => null,
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'name' => 'Cabang Lama',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-03
    | Alamat dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_03_update_branch_empty_address_succeeds(): void
    {
        $data = $this->validBranchData([
            'address' => null,
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'address' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-04
    | Nomor telepon dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_04_update_branch_empty_phone_succeeds(): void
    {
        $data = $this->validBranchData([
            'phone' => null,
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'phone' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-05
    | Nama kepala cabang dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_05_update_branch_empty_head_name_succeeds(): void
    {
        $data = $this->validBranchData([
            'head_name' => null,
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'head_name' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-06
    | Nama cabang 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_06_update_branch_name_255_chars_succeeds(): void
    {
        $data = $this->validBranchData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'name' => str_repeat('A', 255),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-07
    | Nama cabang 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_07_update_branch_name_254_chars_succeeds(): void
    {
        $data = $this->validBranchData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'name' => str_repeat('A', 254),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-08
    | Nama cabang 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_08_update_branch_name_256_chars_fails(): void
    {
        $data = $this->validBranchData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'name' => 'Cabang Lama',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-09
    | Nomor telepon 50 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_09_update_branch_phone_50_chars_succeeds(): void
    {
        $data = $this->validBranchData([
            'phone' => str_repeat('1', 50),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'phone' => str_repeat('1', 50),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-10
    | Nomor telepon 49 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_10_update_branch_phone_49_chars_succeeds(): void
    {
        $data = $this->validBranchData([
            'phone' => str_repeat('1', 49),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'phone' => str_repeat('1', 49),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-11
    | Nomor telepon 51 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_11_update_branch_phone_51_chars_fails(): void
    {
        $data = $this->validBranchData([
            'phone' => str_repeat('1', 51),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertSessionHasErrors('phone');

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'phone' => '08123456789',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-12
    | Nama kepala cabang 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_12_update_branch_head_name_255_chars_succeeds(): void
    {
        $data = $this->validBranchData([
            'head_name' => str_repeat('A', 255),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'head_name' => str_repeat('A', 255),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-13
    | Nama kepala cabang 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_13_update_branch_head_name_254_chars_succeeds(): void
    {
        $data = $this->validBranchData([
            'head_name' => str_repeat('A', 254),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertRedirect(
            route('admin.settings')
        );

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'head_name' => str_repeat('A', 254),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-CB-14
    | Nama kepala cabang 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_cb_14_update_branch_head_name_256_chars_fails(): void
    {
        $data = $this->validBranchData([
            'head_name' => str_repeat('A', 256),
        ]);

        $response = $this->put(
            route('admin.settings.branch.update'),
            $data
        );

        $response->assertSessionHasErrors('head_name');

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'head_name' => 'Kepala Cabang Lama',
        ]);
    }
}