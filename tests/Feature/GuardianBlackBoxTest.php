<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Models\Group;
use App\Models\Assessment;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GuardianBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::create([
            'name' => 'Cabang Test',
        ]);

        $this->branch = $branch;

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

    private function validGuardianData(array $override = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'wali' . uniqid() . '@gmail.com',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 123',
            'relation' => 'Ayah',
        ], $override);
    }

    private function createGuardian(): Guardian
    {
        $user = User::create([
            'name' => 'User Wali Test',
            'email' => 'wali' . uniqid() . '@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'guardian',
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        return Guardian::create([
            'user_id' => $user->id,
            'name' => 'Wali Lama',
            'phone' => '081234567890',
            'relation' => 'Ayah',
            'address' => 'Alamat Lama',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-01
    | Tambah wali santri - semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_01_store_guardian_all_valid_data(): void
    {
        $data = $this->validGuardianData();

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));

        $this->assertDatabaseHas('users', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'guardian',
        ]);

        $this->assertDatabaseHas('guardians', [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'relation' => $data['relation'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-02
    | Nama lengkap kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_02_store_guardian_empty_name_fails(): void
    {
        $data = $this->validGuardianData([
            'name' => '',
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('users', [
            'email' => $data['email'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-03
    | Email kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_03_store_guardian_empty_email_fails(): void
    {
        $data = $this->validGuardianData([
            'email' => '',
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-04
    | Nomor telepon kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_04_store_guardian_empty_phone_succeeds(): void
    {
        $data = $this->validGuardianData([
            'phone' => '',
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'phone' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-05
    | Format email salah
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_05_store_guardian_invalid_email_fails(): void
    {
        $data = $this->validGuardianData([
            'email' => 'walisantri.gmail.com',
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertSessionHasErrors('email');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-06
    | Alamat kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_06_store_guardian_empty_address_succeeds(): void
    {
        $data = $this->validGuardianData([
            'address' => '',
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));

        $this->assertDatabaseHas('guardians', [
            'name' => $data['name'],
            'address' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-07
    | Hubungan kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_07_store_guardian_empty_relation_succeeds(): void
    {
        $data = $this->validGuardianData([
            'relation' => '',
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));

        $this->assertDatabaseHas('guardians', [
            'name' => $data['name'],
            'relation' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-08
    | Nama 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_08_store_guardian_name_254_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-09
    | Nama 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_09_store_guardian_name_255_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-10
    | Nama 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_10_store_guardian_name_256_chars_fails(): void
    {
        $data = $this->validGuardianData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-11
    | Nomor HP 15 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_11_store_guardian_phone_15_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'phone' => str_repeat('1', 15),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-12
    | Nomor HP 14 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_12_store_guardian_phone_14_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'phone' => str_repeat('1', 14),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-13
    | Nomor HP 16 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_13_store_guardian_phone_16_chars_fails(): void
    {
        $data = $this->validGuardianData([
            'phone' => str_repeat('1', 16),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertSessionHasErrors('phone');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-14
    | Hubungan 100 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_14_store_guardian_relation_100_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'relation' => str_repeat('A', 100),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-15
    | Hubungan 101 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_15_store_guardian_relation_101_chars_fails(): void
    {
        $data = $this->validGuardianData([
            'relation' => str_repeat('A', 101),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertSessionHasErrors('relation');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-16
    | Alamat 100 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_16_store_guardian_address_100_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'address' => str_repeat('A', 100),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-17
    | Alamat 99 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_17_store_guardian_address_99_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'address' => str_repeat('A', 99),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        $response->assertRedirect(route('admin.guardians'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-TWS-18
    | Alamat 101 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_18_store_guardian_address_101_chars_succeeds(): void
    {
        $data = $this->validGuardianData([
            'address' => str_repeat('A', 101),
        ]);

        $response = $this->post(
            route('admin.guardians.store'),
            $data
        );

        /*
         * Berdasarkan controller saat ini:
         *
         * 'address' => ['nullable', 'string'],
         *
         * alamat TIDAK mempunyai max:100.
         * Jadi 101 karakter masih BERHASIL.
         */
        $response->assertRedirect(route('admin.guardians'));
    }

        /*
    |--------------------------------------------------------------------------
    | TC-TWS-19
    | Edit - mengubah seluruh data
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_19_update_guardian_all_data_succeeds(): void
    {
        $guardian = $this->createGuardian();

        $emailBaru = 'walibaru' . uniqid() . '@gmail.com';

        $response = $this->put(
            route('admin.guardians.update', $guardian),
            [
                'name' => 'Wali Baru',
                'email' => $emailBaru,
                'phone' => '081298765432',
                'relation' => 'Ibu',
                'address' => 'Alamat Baru',
            ]
        );

        $response->assertRedirect(route('admin.guardians'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('guardians', [
            'id' => $guardian->id,
            'name' => 'Wali Baru',
            'phone' => '081298765432',
            'relation' => 'Ibu',
            'address' => 'Alamat Baru',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $guardian->user_id,
            'name' => 'Wali Baru',
            'email' => $emailBaru,
            'phone' => '081298765432',
            'address' => 'Alamat Baru',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TWS-20
    | Edit - mengubah beberapa data
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_20_update_guardian_some_data_succeeds(): void
    {
        $guardian = $this->createGuardian();

        $emailLama = $guardian->user->email;

        $response = $this->put(
            route('admin.guardians.update', $guardian),
            [
                'name' => 'Wali Diubah',
                'email' => $emailLama,
                'phone' => $guardian->phone,
                'relation' => $guardian->relation,
                'address' => $guardian->address,
            ]
        );

        $response->assertRedirect(route('admin.guardians'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('guardians', [
            'id' => $guardian->id,
            'name' => 'Wali Diubah',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $guardian->user_id,
            'name' => 'Wali Diubah',
            'email' => $emailLama,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TWS-21
    | Edit - nama dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_21_update_guardian_empty_name_address_fails(): void
    {
        $guardian = $this->createGuardian();

        $response = $this->put(
            route('admin.guardians.update', $guardian),
            [
                'name' => '',
                'email' => $guardian->user->email,
                'phone' => $guardian->phone,
                'relation' => $guardian->relation,
                'address' => '',
            ]
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('guardians', [
            'id' => $guardian->id,
            'name' => 'Wali Lama',
            'address' => 'Alamat Lama',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $guardian->user_id,
            'name' => 'User Wali Test',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TWS-22
    | Hapus wali santri tanpa santri
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_22_destroy_guardian_without_student_succeeds(): void
    {
        $guardian = $this->createGuardian();

        $userId = $guardian->user_id;

        $response = $this->delete(
            route('admin.guardians.destroy', $guardian)
        );

        $response->assertRedirect(route('admin.guardians'));

        $response->assertSessionHas(
            'status',
            'Data wali santri berhasil dihapus.'
        );

        $this->assertDatabaseMissing('users', [
            'id' => $userId,
        ]);

        $this->assertDatabaseMissing('guardians', [
            'id' => $guardian->id,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TC-TWS-23
    | Hapus wali santri yang masih terhubung dengan santri
    |--------------------------------------------------------------------------
    */

    public function test_tc_tws_23_destroy_guardian_with_student_fails(): void
    {
        $guardian = $this->createGuardian();

        $group = Group::create([
            'branch_id' => $this->branch->id,
            'name' => 'Kelompok Test',
        ]);

        Student::create([
            'branch_id' => $this->branch->id,
            'group_id' => $group->id,
            'guardian_id' => $guardian->id,
            'name' => 'Santri Test',
        ]);

        $userId = $guardian->user_id;

        $response = $this->delete(
            route('admin.guardians.destroy', $guardian)
        );

        $response->assertSessionHasErrors([
            'guardian' => 'Wali santri masih terhubung dengan santri. Pindahkan data santri terlebih dahulu.',
        ]);

        $this->assertDatabaseHas('guardians', [
            'id' => $guardian->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $userId,
        ]);
    }
}