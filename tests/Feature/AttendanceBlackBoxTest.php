<?php
namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Branch $branch;
    protected Group $group;
    protected Period $period;
    protected Teacher $teacher;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Test',
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

        $this->user = User::factory()->create([
            'branch_id' => $this->branch->id,
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'user_id' => $this->user->id,
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'name' => 'Guru Test',
        ]);

        $this->student = Student::create([
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'name' => 'Santri Test',
            'status' => 'active',
        ]);

        $this->actingAs($this->user);
    }

    private function validAttendanceData(array $override = []): array
    {
        return array_replace_recursive([
            'group_id' => $this->group->id,
            'schedule_id' => null,
            'period_id' => $this->period->id,
            'attendance_date' => '2025-08-01',
            'meeting_number' => 1,
            'start_time' => '07:00',
            'end_time' => '08:00',

            'students' => [
                $this->student->id => [
                    'status' => 'hadir',
                    'note' => null,
                ],
            ],
        ], $override);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-01
    | Catatan 254 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_01_store_attendance_note_254_chars_succeeds(): void
    {
        $note = str_repeat('A', 254);

        $data = $this->validAttendanceData([
            'students' => [
                $this->student->id => [
                    'status' => 'hadir',
                    'note' => $note,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_details', [
            'student_id' => $this->student->id,
            'status' => 'hadir',
            'note' => $note,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-02
    | Catatan 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_02_store_attendance_note_255_chars_succeeds(): void
    {
        $note = str_repeat('A', 255);

        $data = $this->validAttendanceData([
            'students' => [
                $this->student->id => [
                    'status' => 'hadir',
                    'note' => $note,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_details', [
            'student_id' => $this->student->id,
            'status' => 'hadir',
            'note' => $note,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-03
    | Catatan 256 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_03_store_attendance_note_256_chars_fails(): void
    {
        $note = str_repeat('A', 256);

        $data = $this->validAttendanceData([
            'students' => [
                $this->student->id => [
                    'status' => 'hadir',
                    'note' => $note,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertSessionHasErrors(
            'students.' . $this->student->id . '.note'
        );

        $this->assertDatabaseMissing('attendance_details', [
            'student_id' => $this->student->id,
            'note' => $note,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-04
    | Menghapus presensi
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_04_destroy_attendance_succeeds(): void
    {
        $attendance = Attendance::create([
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'teacher_id' => $this->teacher->id,
            'period_id' => $this->period->id,
            'schedule_id' => null,
            'attendance_date' => '2025-08-01',
            'meeting_number' => 1,
            'start_time' => '07:00',
            'end_time' => '08:00',
        ]);

        $response = $this->delete(
            route('teachers.attendance.destroy', $attendance->id)
        );

        $response->assertRedirect(
            route('teachers.attendance')
        );

        $response->assertSessionHas(
            'status',
            'Presensi berhasil dihapus.'
        );

        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-05
    | Semua data presensi valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_05_store_attendance_all_valid_data_succeeds(): void
    {
        $data = $this->validAttendanceData();

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'teacher_id' => $this->teacher->id,
            'period_id' => $this->period->id,
            'attendance_date' => '2025-08-01 00:00:00',
            'meeting_number' => 1,
        ]);

        $this->assertDatabaseHas('attendance_details', [
            'student_id' => $this->student->id,
            'status' => 'hadir',
            'note' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-06
    | Status kehadiran valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_06_store_attendance_valid_status_succeeds(): void
    {
        $data = $this->validAttendanceData([
            'students' => [
                $this->student->id => [
                    'status' => 'hadir',
                    'note' => null,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_details', [
            'student_id' => $this->student->id,
            'status' => 'hadir',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-07
    | Catatan dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_07_store_attendance_empty_note_succeeds(): void
    {
        $data = $this->validAttendanceData([
            'students' => [
                $this->student->id => [
                    'status' => 'hadir',
                    'note' => null,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_details', [
            'student_id' => $this->student->id,
            'status' => 'hadir',
            'note' => null,
        ]);
    }
}