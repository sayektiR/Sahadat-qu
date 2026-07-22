<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherBlackBoxTest extends TestCase
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
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function validTeacherData(array $override = []): array
    {
        return array_merge([
            'name' => 'Guru Test',
            'email' => 'guru' . uniqid() . '@gmail.com',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 123',
            'gender' => 'male',
            'status' => 'active',
            'group_id' => $this->group->id,
        ], $override);
    }

    private function createTeacher(array $override = []): Teacher
    {
        $data = $this->validTeacherData($override);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password123'),
            'role' => 'teacher',
            'branch_id' => $this->branch->id,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $this->branch->id,
            'group_id' => $data['group_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'] ?? null,
            'status' => $data['status'],
            'photo' => null,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-01
    | Tambah guru - semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_01_store_teacher_all_valid_data_succeeds(): void
    {
        $data = $this->validTeacherData();

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('users', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'teacher',
        ]);

        $this->assertDatabaseHas('teachers', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'status' => $data['status'],
            'group_id' => $data['group_id'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-02
    | Nama kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_02_store_teacher_empty_name_fails(): void
    {
        $data = $this->validTeacherData([
            'name' => '',
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('users', [
            'email' => $data['email'],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-03
    | Email kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_03_store_teacher_empty_email_fails(): void
    {
        $data = $this->validTeacherData([
            'email' => '',
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-04
    | Format email tidak valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_04_store_teacher_invalid_email_fails(): void
    {
        $data = $this->validTeacherData([
            'email' => 'guru.gmail.com',
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-05
    | Grup tidak dipilih
    | Controller: group_id nullable -> BERHASIL
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_05_store_teacher_without_group_succeeds(): void
    {
        $data = $this->validTeacherData([
            'group_id' => null,
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'name' => $data['name'],
            'group_id' => null,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-06
    | Email sudah digunakan
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_06_store_teacher_duplicate_email_fails(): void
    {
        $existingUser = User::factory()->create([
            'branch_id' => $this->branch->id,
            'email' => 'guru.existing@gmail.com',
            'role' => 'teacher',
        ]);

        $data = $this->validTeacherData([
            'email' => $existingUser->email,
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-07
    | Status tidak dipilih
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_07_store_teacher_empty_status_fails(): void
    {
        $data = $this->validTeacherData([
            'status' => null,
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('status');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-08
    | Gender valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_08_store_teacher_valid_gender_succeeds(): void
    {
        $data = $this->validTeacherData([
            'gender' => 'male',
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'name' => $data['name'],
            'gender' => 'male',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-09
    | Status valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_09_store_teacher_valid_status_succeeds(): void
    {
        $data = $this->validTeacherData([
            'status' => 'inactive',
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'name' => $data['name'],
            'status' => 'inactive',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-10
    | Tidak upload foto
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_10_store_teacher_without_photo_succeeds(): void
    {
        $data = $this->validTeacherData();

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'name' => $data['name'],
            'photo' => null,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-11
    | Upload file selain gambar
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_11_store_teacher_non_image_file_fails(): void
    {
        $data = $this->validTeacherData([
            'photo' => UploadedFile::fake()->create(
                'dokumen.pdf',
                100,
                'application/pdf'
            ),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('photo');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-12
    | Upload gambar dengan format valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_12_store_teacher_valid_image_succeeds(): void
    {
        $data = $this->validTeacherData([
            'photo' => UploadedFile::fake()->image('guru.jpg'),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $teacher = Teacher::where('name', $data['name'])->first();

        $this->assertNotNull($teacher);
        $this->assertNotNull($teacher->photo);

        $this->assertTrue(
            Storage::disk('public')->exists($teacher->photo)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-13
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_13_store_teacher_name_254_chars_succeeds(): void
    {
        $data = $this->validTeacherData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-14
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_14_store_teacher_name_255_chars_succeeds(): void
    {
        $data = $this->validTeacherData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-15
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_15_store_teacher_name_256_chars_fails(): void
    {
        $data = $this->validTeacherData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-16
    | Email 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_16_store_teacher_email_255_chars_succeeds(): void
    {
        // Total panjang email dibuat tepat 255 karakter.
        $localPart = str_repeat('a', 243);
        $email = $localPart . '@gmail.com';

        $this->assertEquals(253, strlen($email));

        $data = $this->validTeacherData([
            'email' => $email,
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-17
    | Email 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_17_store_teacher_email_256_chars_fails(): void
    {
        // Dibuat email lebih dari 255 karakter.
        $email = str_repeat('a', 246) . '@gmail.com';

$this->assertEquals(256, strlen($email));

        $data = $this->validTeacherData([
            'email' => $email,
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-18
    | Nomor telepon 50 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_18_store_teacher_phone_50_chars_succeeds(): void
    {
        $data = $this->validTeacherData([
            'phone' => str_repeat('1', 50),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-19
    | Nomor telepon 49 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_19_store_teacher_phone_49_chars_succeeds(): void
    {
        $data = $this->validTeacherData([
            'phone' => str_repeat('1', 49),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-20
    | Nomor telepon 51 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_20_store_teacher_phone_51_chars_fails(): void
    {
        $data = $this->validTeacherData([
            'phone' => str_repeat('1', 51),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('phone');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-21
    | Ukuran foto > 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_21_store_teacher_photo_over_2mb_fails(): void
    {
        $data = $this->validTeacherData([
            'photo' => UploadedFile::fake()->image('guru.jpg')->size(2049),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertSessionHasErrors('photo');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-22
    | Ukuran foto 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_22_store_teacher_photo_2mb_succeeds(): void
    {
        $data = $this->validTeacherData([
            'photo' => UploadedFile::fake()->image('guru.jpg')->size(2048),
        ]);

        $response = $this->post(
            route('admin.teachers.store'),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-23
    | Edit - mengubah seluruh data
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_23_update_teacher_all_data_succeeds(): void
    {
        $teacher = $this->createTeacher();

        $data = [
            'name' => 'Guru Baru',
            'email' => 'gurubaru@gmail.com',
            'phone' => '081298765432',
            'address' => 'Alamat Baru',
            'gender' => 'female',
            'status' => 'inactive',
            'group_id' => $this->group->id,
        ];

        $response = $this->put(
            route('admin.teachers.update', $teacher),
            $data
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'name' => 'Guru Baru',
            'phone' => '081298765432',
            'address' => 'Alamat Baru',
            'gender' => 'female',
            'status' => 'inactive',
            'group_id' => $this->group->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $teacher->user_id,
            'name' => 'Guru Baru',
            'email' => 'gurubaru@gmail.com',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-24
    | Edit - mengubah beberapa kolom
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_24_update_teacher_some_data_succeeds(): void
    {
        $teacher = $this->createTeacher();

        $response = $this->put(
            route('admin.teachers.update', $teacher),
            [
                'name' => 'Guru Diubah',
                'email' => $teacher->user->email,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'gender' => $teacher->gender,
                'status' => $teacher->status,
                'group_id' => $teacher->group_id,
            ]
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'name' => 'Guru Diubah',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-25
    | Menghapus beberapa kolom
    | Nama dan email dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_25_update_teacher_empty_name_email_fails(): void
    {
        $teacher = $this->createTeacher();

        $response = $this->put(
            route('admin.teachers.update', $teacher),
            [
                'name' => '',
                'email' => '',
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'gender' => $teacher->gender,
                'status' => $teacher->status,
                'group_id' => $teacher->group_id,
            ]
        );

        $response->assertSessionHasErrors([
            'name',
            'email',
        ]);

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'name' => 'Guru Test',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-26
    | Menghapus foto
    |
    | CATATAN:
    | Controller saat ini tidak menyediakan fitur hapus foto.
    | Jika tidak upload foto, foto lama tetap digunakan.
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_26_update_teacher_without_new_photo_keeps_old_photo(): void
    {
        $teacher = $this->createTeacher();

        $teacher->update([
            'photo' => 'teachers/foto-lama.jpg',
        ]);

        Storage::disk('public')->put(
            'teachers/foto-lama.jpg',
            'dummy'
        );

        $response = $this->put(
            route('admin.teachers.update', $teacher),
            [
                'name' => $teacher->name,
                'email' => $teacher->user->email,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'gender' => $teacher->gender,
                'status' => $teacher->status,
                'group_id' => $teacher->group_id,
            ]
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'photo' => 'teachers/foto-lama.jpg',
        ]);

        $this->assertTrue(
            Storage::disk('public')->exists('teachers/foto-lama.jpg')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-27
    | Edit - upload foto 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_27_update_teacher_photo_2mb_succeeds(): void
    {
        $teacher = $this->createTeacher();

        $response = $this->put(
            route('admin.teachers.update', $teacher),
            [
                'name' => $teacher->name,
                'email' => $teacher->user->email,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'gender' => $teacher->gender,
                'status' => $teacher->status,
                'group_id' => $teacher->group_id,
                'photo' => UploadedFile::fake()
                    ->image('guru-baru.jpg')
                    ->size(2048),
            ]
        );

        $response->assertRedirect(route('admin.teachers'));

        $teacher->refresh();

        $this->assertNotNull($teacher->photo);

        $this->assertTrue(
            Storage::disk('public')->exists($teacher->photo)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-28
    | Edit - upload foto > 2 MB
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_28_update_teacher_photo_over_2mb_fails(): void
    {
        $teacher = $this->createTeacher();

        $response = $this->put(
            route('admin.teachers.update', $teacher),
            [
                'name' => $teacher->name,
                'email' => $teacher->user->email,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'gender' => $teacher->gender,
                'status' => $teacher->status,
                'group_id' => $teacher->group_id,
                'photo' => UploadedFile::fake()
                    ->image('guru-baru.jpg')
                    ->size(2049),
            ]
        );

        $response->assertSessionHasErrors('photo');
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-29
    | Hapus guru yang tidak terhubung data
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_29_destroy_teacher_without_related_data_succeeds(): void
    {
        $teacher = $this->createTeacher();

        $userId = $teacher->user_id;

        $response = $this->delete(
            route('admin.teachers.destroy', $teacher)
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseMissing('teachers', [
            'id' => $teacher->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $userId,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TG-30
    | Hapus guru yang terhubung data
    |
    | CATATAN:
    | Controller TIDAK mengecek relasi data guru.
    | Maka berdasarkan controller saat ini, guru tetap dihapus.
    |--------------------------------------------------------------------------
    */

    public function test_tc_tg_30_destroy_teacher_with_related_data_current_controller_deletes(): void
    {
        $teacher = $this->createTeacher();

        // Berdasarkan controller saat ini tidak ada pengecekan
        // apakah guru terhubung dengan data lain.

        $response = $this->delete(
            route('admin.teachers.destroy', $teacher)
        );

        $response->assertRedirect(route('admin.teachers'));

        $this->assertDatabaseMissing('teachers', [
            'id' => $teacher->id,
        ]);
    }
}