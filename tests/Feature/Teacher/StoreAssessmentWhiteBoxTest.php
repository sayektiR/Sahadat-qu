<?php

namespace Tests\Feature\Teacher;

use App\Models\AssessmentTemplate;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Period;
use App\Models\Assessment;
use App\Models\AssessmentAspect;
use App\Models\Student;
use App\Models\AssessmentAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAssessmentWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2 → 3(False) → 24
     */
    public function test_path_1_teacher_cannot_assess_other_group()
    {
        // Branch
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '081111111111',
            'head_name' => 'Ketua',
        ]);

        // Group guru
        $groupTeacher = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok A',
        ]);

        // Group lain
        $groupOther = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok B',
        ]);

        // User guru
        $user = User::factory()->create([
            'role' => 'teacher',
            'branch_id' => $branch->id,
        ]);

        // Teacher
        Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'group_id' => $groupTeacher->id,
            'name' => 'Guru A',
        ]);

        // Template
        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [

                // sengaja group lain
                'group_id' => $groupOther->id,

                'assessment_template_id' => $template->id,

                'assessment_date' => now()->format('Y-m-d'),

            ]);

        $response->assertForbidden();
    }

    
    public function test_path_2_student_not_in_teacher_group()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '08111111111',
            'head_name' => 'Ketua',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok A',
        ]);

        $group2 = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok B',
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

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        // student milik grup lain
        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group2->id,
            'name' => 'Budi',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [
                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => now()->format('Y-m-d'),

                'scores' => [
                    $student->id => [
                        $aspect->id => 90,
                    ],
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_path_3_store_assessment_success()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '08111111111',
            'head_name' => 'Ketua',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok A',
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
            'name' => 'Semester 1',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        $attribute = AssessmentAttribute::create([
            'assessment_template_id' => $template->id,
            'attribute_name' => 'Adab',
            'attribute_type' => 'text', // atau 'number'
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [

                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => now()->format('Y-m-d'),

                'scores' => [
                    $student->id => [
                        $aspect->id => 90,
                    ],
                ],

                'attributes' => [
                    $attribute->id => 'Baik',
                ],
            ]);

        $response->assertSessionHas('status', 'Penilaian berhasil disimpan.');

        $this->assertDatabaseHas('assessments', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'group_id' => $group->id,
        ]);

        $this->assertDatabaseHas('assessment_scorings', [
            'assessment_aspect_id' => $aspect->id,
            'value' => 90,
        ]);

        $this->assertDatabaseHas('assessment_attribute_values', [
            'assessment_attribute_id' => $attribute->id,
            'value' => 'Baik',
        ]);
    }

    public function test_path_4_update_existing_assessment()
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

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        $attribute = AssessmentAttribute::create([
            'assessment_template_id' => $template->id,
            'attribute_name' => 'Adab',
            'attribute_type' => 'text',
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $date = '2026-07-20';

        // assessment sudah ada
        $assessment = Assessment::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'assessment_template_id' => $template->id,
            'period_id' => $period->id,
            'assessment_date' => $date,
            'final_score' => 50,
            'predicate' => 'C',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [
                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => $date,
                'scores' => [
                    $student->id => [
                        $aspect->id => 95,
                    ],
                ],
                'attributes' => [
                    $attribute->id => 'Baik',
                ],
            ]);

            // dd(Assessment::all()->toArray());
        $response->assertSessionHas('status', 'Penilaian berhasil disimpan.');
        $assessment->refresh();

        $this->assertEquals(95, $assessment->final_score);
    }

    public function test_path_5_aspect_not_in_template()
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

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        // aspect yang VALID
        AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('teachers.assessments.store'), [
            'assessment_template_id' => $template->id,
            'group_id' => $group->id,
            'assessment_date' => now()->toDateString(),
            'scores' => [
                $student->id => [
                    999 => 90, // aspect tidak ada di template
                ],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_path_6_attribute_not_in_template()
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

        Period::create([
            'branch_id' => $branch->id,
            'name' => '2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        // attribute yang valid (supaya template punya attribute)
        AssessmentAttribute::create([
            'assessment_template_id' => $template->id,
            'attribute_name' => 'Adab',
            'attribute_type' => 'text',
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [
                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => now()->toDateString(),
                'scores' => [
                    $student->id => [
                        $aspect->id => 90,
                    ],
                ],
                'attributes' => [
                    999 => 'Baik', // ID attribute tidak ada di template
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_path_7_store_without_attributes()
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

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [
                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => now()->toDateString(),
                'scores' => [
                    $student->id => [
                        $aspect->id => 90,
                    ],
                ],
                // attributes sengaja tidak dikirim
            ]);

        $response->assertSessionHas('status', 'Penilaian berhasil disimpan.');

        $this->assertDatabaseHas('assessments', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'final_score' => 90,
        ]);

        $this->assertDatabaseCount('assessment_attribute_values', 0);
    }

    public function test_path_8_final_score_zero()
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

        Period::create([
            'branch_id' => $branch->id,
            'name' => '2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [
                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => now()->toDateString(),
                'scores' => [
                    $student->id => [
                        $aspect->id => 0,
                    ],
                ],
            ]);

        $response->assertSessionHas('status', 'Penilaian berhasil disimpan.');

        $this->assertDatabaseHas('assessments', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'final_score' => 0,
            'predicate' => 'Perlu Mengulang',
        ]);
    }
    
    public function test_path_9_total_weight_zero()
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

        Period::create([
            'branch_id' => $branch->id,
            'name' => '2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template',
        ]);

        // weight = 0 sehingga totalWeight = 0
        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 0,
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('teachers.assessments.store'), [
                'assessment_template_id' => $template->id,
                'group_id' => $group->id,
                'assessment_date' => now()->toDateString(),
                'scores' => [
                    $student->id => [
                        $aspect->id => 100,
                    ],
                ],
            ]);

        $response->assertSessionHas('status', 'Penilaian berhasil disimpan.');

        $this->assertDatabaseHas('assessments', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'final_score' => 0,
            'predicate' => 'Perlu Mengulang',
        ]);
    }

}