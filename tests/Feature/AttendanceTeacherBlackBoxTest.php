<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTeacherBlackBoxTest extends TestCase
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

    /**
     * Data presensi valid sebagai dasar pengujian.
     */
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
    | Melakukan presensi - Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_01_store_attendance_all_valid_data_succeeds(): void
    {
        $data = $this->validAttendanceData();

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $attendance = Attendance::where('branch_id', $this->branch->id)
            ->where('group_id', $this->group->id)
            ->where('teacher_id', $this->teacher->id)
            ->where('period_id', $this->period->id)
            ->where('meeting_number', 1)
            ->first();

        $this->assertNotNull($attendance);

        $this->assertEquals(
            '2025-08-01',
            date('Y-m-d', strtotime($attendance->attendance_date))
        );

        $this->assertDatabaseHas('attendance_details', [
            'attendance_id' => $attendance->id,
            'student_id' => $this->student->id,
            'status' => 'hadir',
            'note' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-02
    | Status kehadiran valid - sakit
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_02_valid_attendance_status_sakit_succeeds(): void
    {
        $data = $this->validAttendanceData([
            'students' => [
                $this->student->id => [
                    'status' => 'sakit',
                    'note' => null,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $attendance = Attendance::where(
            'teacher_id',
            $this->teacher->id
        )->first();

        $this->assertNotNull($attendance);

        $this->assertDatabaseHas('attendance_details', [
            'attendance_id' => $attendance->id,
            'student_id' => $this->student->id,
            'status' => 'sakit',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-03
    | Jam mulai dan selesai kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_03_empty_start_and_end_time_succeeds(): void
    {
        $data = $this->validAttendanceData([
            'start_time' => null,
            'end_time' => null,
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $attendance = Attendance::where(
            'teacher_id',
            $this->teacher->id
        )->first();

        $this->assertNotNull($attendance);

        $this->assertNull($attendance->start_time);
        $this->assertNull($attendance->end_time);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-04
    | Note kosong
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_04_empty_note_succeeds(): void
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

        $attendance = Attendance::where(
            'teacher_id',
            $this->teacher->id
        )->first();

        $this->assertNotNull($attendance);

        $this->assertDatabaseHas('attendance_details', [
            'attendance_id' => $attendance->id,
            'student_id' => $this->student->id,
            'status' => 'hadir',
            'note' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-05
    | End time sama dengan start time
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_05_end_time_same_as_start_time_fails(): void
    {
        $data = $this->validAttendanceData([
            'start_time' => '08:00',
            'end_time' => '08:00',
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertSessionHasErrors('end_time');

        $this->assertDatabaseCount('attendances', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-06
    | End time melewati batas minimum
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_06_end_time_one_minute_after_start_succeeds(): void
    {
        $data = $this->validAttendanceData([
            'start_time' => '08:00',
            'end_time' => '08:01',
        ]);

        $response = $this->post(
            route('teachers.attendance.store'),
            $data
        );

        $response->assertRedirect();

        $attendance = Attendance::where(
            'teacher_id',
            $this->teacher->id
        )->first();

        $this->assertNotNull($attendance);

        $this->assertEquals(
            '08:00',
            date('H:i', strtotime($attendance->start_time))
        );

        $this->assertEquals(
            '08:01',
            date('H:i', strtotime($attendance->end_time))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-07
    | Note tepat 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_07_note_255_characters_succeeds(): void
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

        $attendance = Attendance::where(
            'teacher_id',
            $this->teacher->id
        )->first();

        $this->assertNotNull($attendance);

        $this->assertDatabaseHas('attendance_details', [
            'attendance_id' => $attendance->id,
            'student_id' => $this->student->id,
            'note' => $note,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-08
    | Note melebihi 255 karakter
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_08_note_256_characters_fails(): void
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

        $this->assertDatabaseCount('attendances', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PRS-09
    | Menghapus presensi
    |--------------------------------------------------------------------------
    */

    public function test_tc_prs_09_destroy_attendance_succeeds(): void
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
            route('teachers.attendance.destroy', $attendance)
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
}