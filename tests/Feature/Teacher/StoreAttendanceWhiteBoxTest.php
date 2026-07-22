<?php

namespace Tests\Feature\Teacher;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAttendanceWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

   private function createData()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '081111111111',
            'head_name' => 'Ketua',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok A',
        ]);

        $period = Period::create([
            'branch_id' => $branch->id,
            'name' => 'Semester Ganjil',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Guru Test',
        ]);

        return compact('branch', 'group', 'period', 'user', 'teacher');
    }

    public function test_path_1_teacher_not_found()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '081111111111',
            'head_name' => 'Ketua',
        ]);

        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.attendance.store'), []);

        $response->assertForbidden();
    }

   
    public function test_path_2_store_attendance_success()
    {
        extract($this->createData());

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.attendance.store'), [
                'group_id' => $group->id,
                'period_id' => $period->id,
                'attendance_date' => '2026-07-20',
                'meeting_number' => 1,

                'students' => [
                    $student->id => [
                        'status' => 'hadir',
                        'note' => 'Masuk',
                    ]
                ],
            ]);

        $response->assertRedirect();

        $attendance = Attendance::first();

        $this->assertDatabaseHas('attendances', [
            'teacher_id' => $teacher->id,
        ]);

        $this->assertDatabaseHas('attendance_details', [
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'status' => 'hadir',
        ]);
    }

    public function test_path_3_student_not_in_teacher_group()
    {
        extract($this->createData());

        $otherGroup = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok B',
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $otherGroup->id,
            'name' => 'Santri Lain',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.attendance.store'), [
                'group_id' => $group->id,
                'period_id' => $period->id,
                'attendance_date' => '2026-07-20',
                'meeting_number' => 1,

                'students' => [
                    $student->id => [
                        'status' => 'hadir',
                    ]
                ],
            ]);

        $response->assertRedirect();

        $attendance = Attendance::first();

        $this->assertDatabaseHas('attendances', [
            'teacher_id' => $teacher->id,
        ]);

        $this->assertDatabaseMissing('attendance_details', [
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
        ]);
    }
}