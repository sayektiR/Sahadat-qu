<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected Group $group;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->branch = Branch::create([
            'name' => 'Cabang Test',
        ]);

        $this->group = Group::create([
            'branch_id' => $this->branch->id,
            'name' => 'Kelompok Test',
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
    | HELPER DATA
    |--------------------------------------------------------------------------
    */

    private function validStudentData(array $override = []): array
    {
        return array_merge([
            'group_id' => $this->group->id,
            'guardian_id' => null,
            'nis' => 'SQ-010101',
            'nik' => '1234567890123456',
            'name' => 'Budi Santoso',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'male',
            'address' => 'Jl. Contoh No. 123',
            'status' => 'active',
        ], $override);
    }

    private function createGuardian(): Guardian
    {
        $user = User::factory()->create([
            'branch_id' => $this->branch->id,
            'role' => 'guardian',
            'is_active' => true,
        ]);

        return Guardian::create([
            'user_id' => $user->id,
            'name' => 'Wali Test',
            'phone' => '081234567890',
            'relation' => 'Ayah',
            'address' => 'Alamat Wali',
        ]);
    }

    private function createStudent(array $override = []): Student
    {
        return Student::create(array_merge([
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'guardian_id' => null,
            'nis' => 'SQ-' . rand(100000, 999999),
            'nik' => str_pad((string) rand(100000000000000, 999999999999999), 16, '0'),
            'name' => 'Santri Lama',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'male',
            'address' => 'Alamat Lama',
            'status' => 'active',
            'photo' => null,
        ], $override));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-01
    | Tambah santri - semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_01_store_student_all_valid_data(): void
    {
        $data = $this->validStudentData();

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'nik' => $data['nik'],
            'group_id' => $data['group_id'],
            'status' => $data['status'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-02
    | NIK kosong
    | Sesuai controller: nullable -> BERHASIL
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_02_store_student_empty_nik_succeeds(): void
    {
        $data = $this->validStudentData([
            'nik' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'name' => $data['name'],
            'nik' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-03
    | Nama lengkap kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_03_store_student_empty_name_fails(): void
    {
        $data = $this->validStudentData([
            'name' => '',
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-04
    | Tempat lahir kosong
    | Sesuai controller: nullable -> BERHASIL
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_04_store_student_empty_birth_place_succeeds(): void
    {
        $data = $this->validStudentData([
            'birth_place' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-05
    | Tanggal lahir kosong
    | Sesuai controller: nullable -> BERHASIL
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_05_store_student_empty_birth_date_succeeds(): void
    {
        $data = $this->validStudentData([
            'birth_date' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-06
    | Kelompok tidak dipilih
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_06_store_student_without_group_fails(): void
    {
        $data = $this->validStudentData([
            'group_id' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('group_id');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-07
    | Status tidak dipilih
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_07_store_student_without_status_fails(): void
    {
        $data = $this->validStudentData([
            'status' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('status');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-08
    | Wali santri tidak dipilih
    | Sesuai controller: nullable -> BERHASIL
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_08_store_student_without_guardian_succeeds(): void
    {
        $data = $this->validStudentData([
            'guardian_id' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-09
    | Gender tidak dipilih
    | Sesuai controller: nullable -> BERHASIL
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_09_store_student_without_gender_succeeds(): void
    {
        $data = $this->validStudentData([
            'gender' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-10
    | Upload file selain gambar
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_10_store_student_non_image_file_fails(): void
    {
        $data = $this->validStudentData([
            'photo' => UploadedFile::fake()->create(
                'document.pdf',
                100,
                'application/pdf'
            ),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('photo');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-11
    | Upload gambar format valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_11_store_student_valid_image_succeeds(): void
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {

            $data = $this->validStudentData([
                'nik' => str_pad((string) rand(100000000000000, 999999999999999), 16, '0'),
                'photo' => UploadedFile::fake()->image(
                    'student.' . $extension
                ),
            ]);

            $response = $this->post(
                route('admin.students.store'),
                $data
            );

            $response->assertRedirect(route('admin.students'));
            $response->assertSessionHasNoErrors();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-12
    | Tidak upload gambar
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_12_store_student_without_photo_succeeds(): void
    {
        $data = $this->validStudentData([
            'photo' => null,
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-13
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_13_store_student_name_254_chars_succeeds(): void
    {
        $data = $this->validStudentData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-14
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_14_store_student_name_255_chars_succeeds(): void
    {
        $data = $this->validStudentData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-15
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_15_store_student_name_256_chars_fails(): void
    {
        $data = $this->validStudentData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-16
    | Tempat lahir 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_16_store_student_birth_place_254_chars_succeeds(): void
    {
        $data = $this->validStudentData([
            'birth_place' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-17
    | Tempat lahir 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_17_store_student_birth_place_255_chars_succeeds(): void
    {
        $data = $this->validStudentData([
            'birth_place' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-18
    | NIK 16 digit
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_18_store_student_nik_16_digits_succeeds(): void
    {
        $data = $this->validStudentData([
            'nik' => '1234567890123456',
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-19
    | NIK 15 digit
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_19_store_student_nik_15_digits_fails(): void
    {
        $data = $this->validStudentData([
            'nik' => '123456789012345',
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('nik');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-20
    | NIK 17 digit
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_20_store_student_nik_17_digits_fails(): void
    {
        $data = $this->validStudentData([
            'nik' => '12345678901234567',
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('nik');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-21
    | Upload foto sekitar 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_21_store_student_photo_2mb_succeeds(): void
    {
        $data = $this->validStudentData([
            'photo' => UploadedFile::fake()->image('student.jpg')->size(2048),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-22
    | Upload foto > 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_22_store_student_photo_over_2mb_fails(): void
    {
        $data = $this->validStudentData([
            'photo' => UploadedFile::fake()->image('student.jpg')->size(2049),
        ]);

        $response = $this->post(
            route('admin.students.store'),
            $data
        );

        $response->assertSessionHasErrors('photo');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-23
    | Edit - ubah seluruh data
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_23_update_student_all_data_succeeds(): void
    {
        $student = $this->createStudent();

        $data = $this->validStudentData([
            'nis' => $student->nis,
            'nik' => $student->nik,
            'name' => 'Santri Baru',
            'birth_place' => 'Bandung',
            'birth_date' => '2011-02-02',
            'gender' => 'female',
            'address' => 'Alamat Baru',
            'status' => 'inactive',
        ]);

        $response = $this->put(
            route('admin.students.update', $student),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Santri Baru',
            'birth_place' => 'Bandung',
            'status' => 'inactive',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-24
    | Edit - ubah beberapa kolom
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_24_update_student_some_data_succeeds(): void
    {
        $student = $this->createStudent();

        $response = $this->put(
            route('admin.students.update', $student),
            [
                'group_id' => $student->group_id,
                'guardian_id' => $student->guardian_id,
                'nis' => $student->nis,
                'nik' => $student->nik,
                'name' => 'Santri Diubah',
                'birth_place' => $student->birth_place,
                'birth_date' => $student->birth_date,
                'gender' => $student->gender,
                'address' => $student->address,
                'status' => $student->status,
            ]
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Santri Diubah',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-25
    | Edit - nama dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_25_update_student_empty_name_fails(): void
    {
        $student = $this->createStudent();

        $data = [
            'group_id' => $student->group_id,
            'guardian_id' => $student->guardian_id,
            'nis' => $student->nis,
            'nik' => $student->nik,
            'name' => '',
            'birth_place' => $student->birth_place,
            'birth_date' => $student->birth_date,
            'gender' => $student->gender,
            'address' => '',
            'status' => $student->status,
        ];

        $response = $this->put(
            route('admin.students.update', $student),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Santri Lama',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-26
    | Menghapus foto
    |
    | Catatan:
    | Controller saat ini TIDAK menyediakan mekanisme hapus foto.
    | Jika tidak upload foto baru, foto lama dipertahankan.
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_26_update_student_without_new_photo_keeps_old_photo(): void
    {
        $oldPhoto = 'students/foto-lama.jpg';

        Storage::disk('public')->put(
            $oldPhoto,
            'dummy image'
        );

        $student = $this->createStudent([
            'photo' => $oldPhoto,
        ]);

        $response = $this->put(
            route('admin.students.update', $student),
            [
                'group_id' => $student->group_id,
                'guardian_id' => null,
                'nis' => $student->nis,
                'nik' => $student->nik,
                'name' => $student->name,
                'birth_place' => $student->birth_place,
                'birth_date' => $student->birth_date,
                'gender' => $student->gender,
                'address' => $student->address,
                'status' => $student->status,
            ]
        );

        $response->assertRedirect(route('admin.students'));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'photo' => $oldPhoto,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($oldPhoto));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-27
    | Edit foto 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_27_update_student_photo_2mb_succeeds(): void
    {
        $student = $this->createStudent();

        $data = [
            'group_id' => $student->group_id,
            'guardian_id' => null,
            'nis' => $student->nis,
            'nik' => $student->nik,
            'name' => $student->name,
            'birth_place' => $student->birth_place,
            'birth_date' => $student->birth_date,
            'gender' => $student->gender,
            'address' => $student->address,
            'status' => $student->status,
            'photo' => UploadedFile::fake()->image('new-photo.jpg')->size(2048),
        ];

        $response = $this->put(
            route('admin.students.update', $student),
            $data
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-28
    | Edit foto > 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_28_update_student_photo_over_2mb_fails(): void
    {
        $student = $this->createStudent();

        $data = [
            'group_id' => $student->group_id,
            'guardian_id' => null,
            'nis' => $student->nis,
            'nik' => $student->nik,
            'name' => $student->name,
            'birth_place' => $student->birth_place,
            'birth_date' => $student->birth_date,
            'gender' => $student->gender,
            'address' => $student->address,
            'status' => $student->status,
            'photo' => UploadedFile::fake()->image('new-photo.jpg')->size(2049),
        ];

        $response = $this->put(
            route('admin.students.update', $student),
            $data
        );

        $response->assertSessionHasErrors('photo');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-29
    | Hapus santri tanpa data terkait
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_29_destroy_student_without_related_data_succeeds(): void
    {
        $student = $this->createStudent();

        $response = $this->delete(
            route('admin.students.destroy', $student)
        );

        $response->assertRedirect(route('admin.students'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TS-30
    | Hapus santri yang memiliki data assessment
    |--------------------------------------------------------------------------
    */

    public function test_tc_ts_30_destroy_student_with_assessment_fails(): void
    {
        $student = $this->createStudent();

        /*
         * Buat Assessment di sini sesuai struktur model Assessment
         * yang digunakan project.
         *
         * Contoh jika assessment membutuhkan student_id:
         *
         * Assessment::create([
         *     'student_id' => $student->id,
         *     ...
         * ]);
         */

        $this->markTestSkipped(
            'Tambahkan data Assessment yang valid sesuai struktur tabel project.'
        );
    }
}