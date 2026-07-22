<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Subject;
use App\Models\User;
use App\Models\ScheduleDetail;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SubjectBlackBoxTest extends TestCase
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

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin' . uniqid() . '@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);
    }

    /*
    |--------------------------------------------------------------------------
    | DATA HELPER
    |--------------------------------------------------------------------------
    */

    private function validSubjectData(array $override = []): array
    {
        return array_merge([
            'name' => 'Matematika',
            'description' => 'Mata pelajaran Matematika',
        ], $override);
    }

    private function createSubject(array $override = []): Subject
    {
        return Subject::create(array_merge([
            'branch_id' => $this->branch->id,
            'name' => 'Matematika ' . uniqid(),
            'description' => 'Deskripsi mata pelajaran',
        ], $override));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-01
    | Tambah Mata Pelajaran - Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_01_store_subject_all_valid_data_succeeds(): void
    {
        $data = $this->validSubjectData();

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-02
    | Nama kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_02_store_subject_empty_name_fails(): void
    {
        $data = $this->validSubjectData([
            'name' => '',
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('subjects', [
            'name' => '',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-03
    | Nama sudah digunakan pada cabang yang sama
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_03_store_subject_duplicate_name_same_branch_fails(): void
    {
        $subject = $this->createSubject([
            'name' => 'Matematika',
        ]);

        $data = $this->validSubjectData([
            'name' => $subject->name,
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('subjects', 1);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-04
    | Deskripsi dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_04_store_subject_empty_description_succeeds(): void
    {
        $data = $this->validSubjectData([
            'description' => null,
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'description' => null,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-05
    | Mengubah data tanpa mengubah nama
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_05_update_subject_same_name_succeeds(): void
    {
        $subject = $this->createSubject([
            'name' => 'Matematika',
            'description' => 'Deskripsi Lama',
        ]);

        $response = $this->put(
            route('admin.subjects.update', $subject),
            [
                'name' => 'Matematika',
                'description' => 'Deskripsi Baru',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Matematika',
            'description' => 'Deskripsi Baru',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-06
    | Nama 99 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_06_store_subject_name_99_chars_succeeds(): void
    {
        $data = $this->validSubjectData([
            'name' => str_repeat('A', 99),
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'name' => $data['name'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-07
    | Nama 100 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_07_store_subject_name_100_chars_succeeds(): void
    {
        $data = $this->validSubjectData([
            'name' => str_repeat('A', 100),
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'name' => $data['name'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-08
    | Nama 101 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_08_store_subject_name_101_chars_fails(): void
    {
        $data = $this->validSubjectData([
            'name' => str_repeat('A', 101),
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-09
    | Deskripsi 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_09_store_subject_description_254_chars_succeeds(): void
    {
        $data = $this->validSubjectData([
            'description' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-10
    | Deskripsi 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_10_store_subject_description_255_chars_succeeds(): void
    {
        $data = $this->validSubjectData([
            'description' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-11
    | Deskripsi 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_11_store_subject_description_256_chars_fails(): void
    {
        $data = $this->validSubjectData([
            'description' => str_repeat('A', 256),
        ]);

        $response = $this->post(
            route('admin.subjects.store'),
            $data
        );

        $response->assertSessionHasErrors('description');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-12
    | Edit Data Mata Pelajaran - seluruh data
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_12_update_subject_all_data_succeeds(): void
    {
        $subject = $this->createSubject([
            'name' => 'Matematika Lama',
            'description' => 'Deskripsi Lama',
        ]);

        $response = $this->put(
            route('admin.subjects.update', $subject),
            [
                'name' => 'Matematika Baru',
                'description' => 'Deskripsi Baru',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Matematika Baru',
            'description' => 'Deskripsi Baru',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-13
    | Edit Data Mata Pelajaran - nama dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_13_update_subject_empty_name_fails(): void
    {
        $subject = $this->createSubject([
            'name' => 'Matematika',
            'description' => 'Deskripsi Lama',
        ]);

        $response = $this->put(
            route('admin.subjects.update', $subject),
            [
                'name' => '',
                'description' => 'Deskripsi Baru',
            ]
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Matematika',
            'description' => 'Deskripsi Lama',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-14
    | Menghapus mata pelajaran yang masih digunakan
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_14_destroy_subject_with_schedule_detail_fails(): void
    {
        $subject = $this->createSubject();

        $group = Group::create([
            'branch_id' => $this->branch->id,
            'name' => 'Kelompok Test',
        ]);

        $period = Period::create([
            'branch_id' => $this->branch->id,
            'name' => 'Periode Test',
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'branch_id' => $this->branch->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'total_meetings' => 16,
        ]);

        ScheduleDetail::create([
            'schedule_id' => $schedule->id,
            'subject_id' => $subject->id,
            'day' => 'Senin',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.subjects.destroy', $subject));

        $response->assertSessionHasErrors([
            'subject' => 'Mata pelajaran masih digunakan.'
        ]);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-MP-15
    | Menghapus mata pelajaran yang tidak digunakan
    |--------------------------------------------------------------------------
    */

    public function test_tc_mp_15_destroy_subject_without_schedule_detail_succeeds(): void
    {
        $subject = $this->createSubject();

        $response = $this->delete(
            route('admin.subjects.destroy', $subject)
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
    }
}