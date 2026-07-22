<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected Group $group;
    protected Period $period;
    protected Subject $subject;

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

        $this->group = Group::create([
            'branch_id' => $this->branch->id,
            'name' => 'Kelompok Test',
        ]);

        $this->period = Period::create([
            'branch_id' => $this->branch->id,
            'name' => 'Periode Test',
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'branch_id' => $this->branch->id,
            'name' => 'Matematika',
            'description' => 'Mata pelajaran matematika',
        ]);

        $this->actingAs($this->admin);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function validScheduleData(array $override = []): array
    {
        return array_replace_recursive([
            'group_id' => $this->group->id,
            'all_groups' => false,

            'period_id' => $this->period->id,

            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',

            'start_time' => '07:00',
            'end_time' => '08:00',

            'total_meetings' => 16,

            'details' => [
                'Senin' => [
                    [
                        'subject_id' => $this->subject->id,
                        'material_name' => 'Materi Matematika',
                    ],
                ],
            ],
        ], $override);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-02
    | Tambah Jadwal - Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_02_store_schedule_all_valid_data_succeeds(): void
    {
        $data = $this->validScheduleData();

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertRedirect(route('admin.schedules'));

        $this->assertDatabaseHas('schedules', [
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'period_id' => $this->period->id,
            'all_groups' => 0,
            'start_time' => '07:00',
            'end_time' => '08:00',
            'total_meetings' => 16,
        ]);

        $schedule = Schedule::latest('id')->first();

        $this->assertNotNull($schedule);

        $this->assertDatabaseHas('schedule_details', [
            'schedule_id' => $schedule->id,
            'day' => 'Senin',
            'subject_id' => $this->subject->id,
            'material_name' => 'Materi Matematika',
            'order_number' => 1,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-03
    | Periode tidak dipilih
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_03_store_schedule_without_period_fails(): void
    {
        $data = $this->validScheduleData([
            'period_id' => '',
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertSessionHasErrors('period_id');

        $this->assertDatabaseCount('schedules', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-04
    | Kelompok tidak dipilih dan all_groups tidak dicentang
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_04_store_schedule_without_group_and_all_groups_fails(): void
    {
        $data = $this->validScheduleData([
            'group_id' => null,
            'all_groups' => false,
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertSessionHasErrors('group_id');

        $this->assertDatabaseCount('schedules', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-05
    | Berlaku untuk semua kelompok
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_05_store_schedule_for_all_groups_succeeds(): void
    {
        $data = $this->validScheduleData([
            'group_id' => null,
            'all_groups' => true,
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertRedirect(route('admin.schedules'));

        $this->assertDatabaseHas('schedules', [
            'branch_id' => $this->branch->id,
            'group_id' => null,
            'all_groups' => 1,
            'period_id' => $this->period->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-06
    | Jam selesai lebih awal dari jam mulai
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_06_store_schedule_end_time_before_start_time_fails(): void
    {
        $data = $this->validScheduleData([
            'start_time' => '08:00',
            'end_time' => '07:00',
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertSessionHasErrors('end_time');

        $this->assertDatabaseCount('schedules', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-07
    | Materi tidak dipilih
    |--------------------------------------------------------------------------
    |
    | Controller mengizinkan details kosong.
    | Jadi jadwal tetap berhasil dibuat tanpa detail materi.
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_07_store_schedule_without_subject_succeeds(): void
    {
        $data = $this->validScheduleData();

        // Materi/mapel tidak dipilih
        unset($data['details']);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertRedirect(route('admin.schedules'));

        $this->assertDatabaseHas('schedules', [
            'branch_id' => $this->branch->id,
            'period_id' => $data['period_id'],
            'all_groups' => $data['all_groups'] ? 1 : 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-08
    | Material name 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_08_store_schedule_material_name_254_chars_succeeds(): void
    {
        $data = $this->validScheduleData([
            'details' => [
                'Senin' => [
                    [
                        'subject_id' => $this->subject->id,
                        'material_name' => str_repeat('A', 254),
                    ],
                ],
            ],
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertRedirect(route('admin.schedules'));

        $schedule = Schedule::latest('id')->first();

        $this->assertNotNull($schedule);

        $this->assertDatabaseHas('schedule_details', [
            'schedule_id' => $schedule->id,
            'material_name' => str_repeat('A', 254),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-09
    | Material name 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_09_store_schedule_material_name_255_chars_succeeds(): void
    {
        $data = $this->validScheduleData([
            'details' => [
                'Senin' => [
                    [
                        'subject_id' => $this->subject->id,
                        'material_name' => str_repeat('A', 255),
                    ],
                ],
            ],
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertRedirect(route('admin.schedules'));

        $schedule = Schedule::latest('id')->first();

        $this->assertNotNull($schedule);

        $this->assertDatabaseHas('schedule_details', [
            'schedule_id' => $schedule->id,
            'material_name' => str_repeat('A', 255),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-10
    | Material name 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_10_store_schedule_material_name_256_chars_fails(): void
    {
        $data = $this->validScheduleData([
            'details' => [
                'Senin' => [
                    [
                        'subject_id' => $this->subject->id,
                        'material_name' => str_repeat('A', 256),
                    ],
                ],
            ],
        ]);

        $response = $this->post(
            route('admin.schedules.store'),
            $data
        );

        $response->assertSessionHasErrors(
            'details.Senin.0.material_name'
        );

        $this->assertDatabaseCount('schedules', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-JD-11
    | Menghapus jadwal
    |--------------------------------------------------------------------------
    */

    public function test_tc_jd_11_destroy_schedule_succeeds(): void
    {
        $schedule = Schedule::create([
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'all_groups' => false,
            'period_id' => $this->period->id,
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'start_time' => '07:00',
            'end_time' => '08:00',
            'total_meetings' => 16,
        ]);

        $response = $this->delete(
            route('admin.schedules.destroy', $schedule)
        );

        $response->assertRedirect(route('admin.schedules'));

        $this->assertDatabaseMissing('schedules', [
            'id' => $schedule->id,
        ]);
    }
}