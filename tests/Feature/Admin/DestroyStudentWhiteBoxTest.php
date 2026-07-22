<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Period;
use App\Models\AssessmentTemplate;
use App\Models\Assessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyStudentWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2(True) → 3(True) → 4
     */
    public function test_path_1_student_has_related_data()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $guardianUser = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian',
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'guardian_id' => $guardian->id,
            'nis' => 'SQ001',
            'nik' => '1234567890123456',
            'name' => 'Santri',
            'status' => 'active',
        ]);

                $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        $student->update([
            'group_id' => $group->id,
        ]);

        $teacherUser = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'branch_id' => $branch->id,
            'name' => 'Guru',
        ]);

        $period = Period::create([
            'branch_id' => $branch->id,
            'name' => 'Periode 1',
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $template = AssessmentTemplate::create([
            'name' => 'Template',
        ]);

        Assessment::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'period_id' => $period->id,
            'assessment_template_id' => $template->id,
            'assessment_date' => now(),
        ]);

        // Mock salah satu relasi agar exists() = true

        // Jika kamu punya data Assessment, Report, atau AttendanceDetail,
        // lebih baik buat datanya di sini daripada memakai mock.

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.students'))
            ->delete(route('admin.students.destroy', $student));

        $response->assertSessionHasErrors('student');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
        ]);
    }

    /**
     * Path 2
     * 1 → 2(True) → 3(False) → 5 → 6
     */
    public function test_path_2_student_without_related_data()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $guardianUser = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian',
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'guardian_id' => $guardian->id,
            'nis' => 'SQ002',
            'nik' => '1111222233334444',
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.students.destroy', $student));

        $response->assertRedirect(route('admin.students'));

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }

    /**
     * Path 3
     * 1 → 2(False) → abort(403)
     */
    public function test_path_3_student_different_branch()
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
        ]);

        $group = Group::create([
            'branch_id' => $branchB->id,
            'name' => 'Grup B',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branchA->id,
            'role' => 'admin',
        ]);

        $guardianUser = User::factory()->create([
            'branch_id' => $branchB->id,
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian',
        ]);

        $student = Student::create([
            'branch_id' => $branchB->id,
            'group_id' => $group->id,
            'guardian_id' => $guardian->id,
            'nis' => 'SQ003',
            'nik' => '9999888877776666',
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.students.destroy', $student));

        $response->assertForbidden();
    }
}