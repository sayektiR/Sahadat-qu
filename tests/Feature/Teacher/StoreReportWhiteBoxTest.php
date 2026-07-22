<?php

namespace Tests\Feature\Teacher;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Branch;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Group;
use App\Models\Student;
use App\Models\Period;
use App\Models\Report;
use Tests\TestCase;

class StoreReportWhiteBoxTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_path_1_teacher_not_found()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('teachers.reports'));

        $response->assertForbidden();
    }

    public function test_path_2_without_active_period()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'A',
        ]);

        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Guru',
        ]);

        Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Ahmad',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('teachers.reports'));

        $response->assertOk();

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_path_3_create_report_success()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'A',
        ]);

        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Guru',
        ]);

        $period = Period::create([
            'branch_id' => $branch->id,
            'name' => '2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Ahmad',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('teachers.reports'));

        $response->assertOk();

        $this->assertDatabaseHas('reports', [
            'student_id' => $student->id,
            'period_id' => $period->id,
            'homeroom_teacher_id' => $teacher->id,
        ]);
    }

    public function test_path_4_filter_period()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'A',
        ]);

        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Guru',
        ]);

        $period = Period::create([
            'branch_id' => $branch->id,
            'name' => '2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Ahmad',
            'status' => 'active',
        ]);

        Report::create([
            'branch_id' => $branch->id,
            'student_id' => $student->id,
            'period_id' => $period->id,
            'homeroom_teacher_id' => $teacher->id,
            'report_date' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('teachers.reports', [
                'period_id' => $period->id,
            ]));

        $response->assertOk();

        $response->assertViewHas('reports');
    }
}
