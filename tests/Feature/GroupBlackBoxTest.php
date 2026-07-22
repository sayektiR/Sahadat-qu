<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GroupBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Test',
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
    | DATA HELPER
    |--------------------------------------------------------------------------
    */

    private function validGroupData(array $override = []): array
    {
        return array_merge([
            'name' => 'Kelompok Test',
            'description' => 'Deskripsi kelompok test',
        ], $override);
    }

    private function createGroup(array $override = []): Group
    {
        return Group::create(array_merge([
            'branch_id' => $this->branch->id,
            'name' => 'Kelompok Lama ' . uniqid(),
            'description' => 'Deskripsi lama',
        ], $override));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-01
    | Tambah kelompok - semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_01_store_group_all_valid_data_succeeds(): void
    {
        $data = $this->validGroupData();

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('groups', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-02
    | Nama kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_02_store_group_empty_name_fails(): void
    {
        $data = $this->validGroupData([
            'name' => '',
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('groups', [
            'branch_id' => $this->branch->id,
            'description' => $data['description'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-03
    | Nama sudah digunakan pada cabang yang sama
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_03_store_group_duplicate_name_same_branch_fails(): void
    {
        $group = $this->createGroup([
            'name' => 'Kelompok A',
        ]);

        $data = $this->validGroupData([
            'name' => $group->name,
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('groups', 1);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-04
    | Deskripsi dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_04_store_group_empty_description_succeeds(): void
    {
        $data = $this->validGroupData([
            'description' => null,
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('groups', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'description' => null,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-05
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_05_store_group_name_254_chars_succeeds(): void
    {
        $data = $this->validGroupData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-06
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_06_store_group_name_255_chars_succeeds(): void
    {
        $data = $this->validGroupData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-07
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_07_store_group_name_256_chars_fails(): void
    {
        $data = $this->validGroupData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('groups', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-08
    | Deskripsi 999 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_08_store_group_description_999_chars_succeeds(): void
    {
        $data = $this->validGroupData([
            'description' => str_repeat('A', 999),
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-09
    | Deskripsi 1000 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_09_store_group_description_1000_chars_succeeds(): void
    {
        $data = $this->validGroupData([
            'description' => str_repeat('A', 1000),
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-10
    | Deskripsi 1001 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_10_store_group_description_1001_chars_fails(): void
    {
        $data = $this->validGroupData([
            'description' => str_repeat('A', 1001),
        ]);

        $response = $this->post(
            route('admin.groups.store'),
            $data
        );

        $response->assertSessionHasErrors('description');

        $this->assertDatabaseMissing('groups', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-11
    | Edit data kelompok keseluruhan
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_11_update_group_all_data_succeeds(): void
    {
        $group = $this->createGroup();

        $response = $this->put(
            route('admin.groups.update', $group),
            [
                'name' => 'Kelompok Baru',
                'description' => 'Deskripsi Baru',
            ]
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Kelompok Baru',
            'description' => 'Deskripsi Baru',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-12
    | Edit data kelompok dengan nama yang sama
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_12_update_group_without_changing_name_succeeds(): void
    {
        $group = $this->createGroup([
            'name' => 'Kelompok Tetap',
            'description' => 'Deskripsi Lama',
        ]);

        $response = $this->put(
            route('admin.groups.update', $group),
            [
                'name' => 'Kelompok Tetap',
                'description' => 'Deskripsi Baru',
            ]
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Kelompok Tetap',
            'description' => 'Deskripsi Baru',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-13
    | Menghapus kelompok yang masih digunakan
    |
    | Contoh: kelompok memiliki santri
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_13_destroy_group_with_student_fails(): void
    {
        $group = $this->createGroup();

        Student::create([
            'branch_id' => $this->branch->id,
            'group_id' => $group->id,
            'name' => 'Santri Test',
        ]);

        $response = $this->delete(
            route('admin.groups.destroy', $group)
        );

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-14
    | Nama kelompok dikosongkan saat edit
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_14_update_group_empty_name_fails(): void
    {
        $group = $this->createGroup([
            'name' => 'Kelompok Lama',
            'description' => 'Deskripsi Lama',
        ]);

        $response = $this->put(
            route('admin.groups.update', $group),
            [
                'name' => '',
                'description' => 'Deskripsi Baru',
            ]
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Kelompok Lama',
            'description' => 'Deskripsi Lama',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-GP-15
    | Menghapus kelompok yang tidak memiliki relasi
    |--------------------------------------------------------------------------
    */

    public function test_tc_gp_15_destroy_group_without_relation_succeeds(): void
    {
        $group = $this->createGroup();

        $response = $this->delete(
            route('admin.groups.destroy', $group)
        );

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }
}