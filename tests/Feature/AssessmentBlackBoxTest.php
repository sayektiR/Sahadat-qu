<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAspect;
use App\Models\AssessmentTemplate;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Branch $branch;
    protected Group $group;
    protected Period $period;
    protected Teacher $teacher;
    protected Student $student;
    protected AssessmentTemplate $template;
    protected AssessmentAspect $aspect;

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

        $this->template = AssessmentTemplate::create([
            'branch_id' => $this->branch->id,
            'name' => 'Penilaian Test',
        ]);

        $this->aspect = AssessmentAspect::create([
            'assessment_template_id' => $this->template->id,
            'aspect_name' => 'Hafalan',
            'weight' => 100,
        ]);

        /*
         * PENTING:
         * Kolomnya adalah aspect_name, bukan name.
         */

        $this->actingAs($this->user);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function validAssessmentData(array $override = []): array
    {
        return array_replace_recursive([
            'assessment_template_id' => $this->template->id,
            'group_id' => $this->group->id,
            'assessment_date' => '2025-08-01',
            'note' => 'Catatan Test',

            'scores' => [
                $this->student->id => [
                    $this->aspect->id => 80,
                ],
            ],
        ], $override);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-01
    | Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_01_store_assessment_all_valid_data_succeeds(): void
    {
        $data = $this->validAssessmentData();

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertRedirect();

        $assessment = Assessment::where('student_id', $this->student->id)
            ->where('assessment_template_id', $this->template->id)
            ->first();

        $this->assertNotNull($assessment);

        $this->assertEquals(
            '2025-08-01',
            $assessment->assessment_date->format('Y-m-d')
        );

        $this->assertEquals(
            'Catatan Test',
            $assessment->note
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-02
    | Jenis penilaian tidak dipilih
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_02_assessment_template_not_selected_fails(): void
    {
        $data = $this->validAssessmentData([
            'assessment_template_id' => null,
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertSessionHasErrors(
            'assessment_template_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-03
    | Data nilai tidak dikirim
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_03_scores_not_sent_fails(): void
    {
        $data = $this->validAssessmentData([
            'scores' => null,
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertSessionHasErrors('scores');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-04
    | Catatan dikosongkan
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_04_empty_note_succeeds(): void
    {
        $data = $this->validAssessmentData([
            'note' => null,
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertRedirect();

        $assessment = Assessment::where('student_id', $this->student->id)
            ->where('assessment_template_id', $this->template->id)
            ->first();

        $this->assertNotNull($assessment);
        $this->assertNull($assessment->note);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-05
    | Nilai = 0
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_05_score_zero_succeeds(): void
    {
        $data = $this->validAssessmentData([
            'scores' => [
                $this->student->id => [
                    $this->aspect->id => 0,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('assessment_scorings', [
            'assessment_aspect_id' => $this->aspect->id,
            'value' => 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-06
    | Nilai = 1
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_06_score_one_succeeds(): void
    {
        $data = $this->validAssessmentData([
            'scores' => [
                $this->student->id => [
                    $this->aspect->id => 1,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('assessment_scorings', [
            'assessment_aspect_id' => $this->aspect->id,
            'value' => 1,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-07
    | Nilai = 99
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_07_score_99_succeeds(): void
    {
        $data = $this->validAssessmentData([
            'scores' => [
                $this->student->id => [
                    $this->aspect->id => 99,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('assessment_scorings', [
            'assessment_aspect_id' => $this->aspect->id,
            'value' => 99,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-08
    | Nilai = 100
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_08_score_100_succeeds(): void
    {
        $data = $this->validAssessmentData([
            'scores' => [
                $this->student->id => [
                    $this->aspect->id => 100,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('assessment_scorings', [
            'assessment_aspect_id' => $this->aspect->id,
            'value' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-09
    | Nilai = -1
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_09_score_minus_one_fails(): void
    {
        $data = $this->validAssessmentData([
            'scores' => [
                $this->student->id => [
                    $this->aspect->id => -1,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertSessionHasErrors(
            'scores.' . $this->student->id . '.' . $this->aspect->id
        );

        $this->assertDatabaseMissing('assessment_scorings', [
            'assessment_aspect_id' => $this->aspect->id,
            'value' => -1,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-10
    | Nilai = 101
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_10_score_101_fails(): void
    {
        $data = $this->validAssessmentData([
            'scores' => [
                $this->student->id => [
                    $this->aspect->id => 101,
                ],
            ],
        ]);

        $response = $this->post(
            route('teachers.assessments.store'),
            $data
        );

        $response->assertSessionHasErrors(
            'scores.' . $this->student->id . '.' . $this->aspect->id
        );

        $this->assertDatabaseMissing('assessment_scorings', [
            'assessment_aspect_id' => $this->aspect->id,
            'value' => 101,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-GPL-11
    | Menghapus penilaian
    |--------------------------------------------------------------------------
    */

    public function test_tc_gpl_11_destroy_assessment_succeeds(): void
    {
        $assessment = Assessment::create([
            'student_id' => $this->student->id,
            'branch_id' => $this->branch->id,
            'group_id' => $this->group->id,
            'teacher_id' => $this->teacher->id,
            'period_id' => $this->period->id,
            'assessment_template_id' => $this->template->id,
            'assessment_date' => '2025-08-01',
            'note' => 'Catatan Test',
        ]);

        $response = $this->delete(
            route('teachers.assessments.destroy', $assessment)
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'success',
            'Data penilaian berhasil dihapus.'
        );

        $this->assertDatabaseMissing('assessments', [
            'id' => $assessment->id,
        ]);
    }
}