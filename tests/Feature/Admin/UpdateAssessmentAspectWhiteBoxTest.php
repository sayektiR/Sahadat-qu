<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentAspect;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateAssessmentAspectWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    // Path 1: Branch template tidak sesuai dengan branch admin
    public function test_path_1_branch_not_match()
    {
        $branch1 = Branch::create([
            'name' => 'Cabang 1',
            'address' => 'Alamat 1',
            'phone' => '08111',
            'head_name' => 'Ketua 1',
        ]);

        $branch2 = Branch::create([
            'name' => 'Cabang 2',
            'address' => 'Alamat 2',
            'phone' => '08222',
            'head_name' => 'Ketua 2',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch1->id,
        ]);

        $assessmentTemplate = AssessmentTemplate::create([
            'branch_id' => $branch2->id,
            'name' => 'Template Penilaian',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Pengetahuan',
            'weight' => 50,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'admin.settings.assessments.assessment-template.aspects.update',
                    [
                        'assessmentTemplate' => $assessmentTemplate,
                        'aspect' => $aspect,
                    ]
                ),
                [
                    'aspect_name' => 'Pengetahuan Baru',
                    'weight' => 50,
                ]
            );

        $response->assertForbidden();
    }

    // Path 2: Branch sesuai tetapi aspect bukan milik template
    public function test_path_2_aspect_not_belong_to_template()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $template1 = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template 1',
        ]);

        $template2 = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template 2',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $template2->id,
            'aspect_name' => 'Pengetahuan',
            'weight' => 50,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'admin.settings.assessments.assessment-template.aspects.update',
                    [
                        'assessmentTemplate' => $template1,
                        'aspect' => $aspect,
                    ]
                ),
                [
                    'aspect_name' => 'Pengetahuan Baru',
                    'weight' => 50,
                ]
            );

        $response->assertNotFound();
    }

    // Path 3: Total bobot setelah update melebihi 100
    public function test_path_3_total_weight_exceeds_100()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $assessmentTemplate = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template Penilaian',
        ]);

        $aspect1 = AssessmentAspect::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Pengetahuan',
            'weight' => 60,
        ]);

        $aspect2 = AssessmentAspect::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Keterampilan',
            'weight' => 30,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'admin.settings.assessments.assessment-template.aspects.update',
                    [
                        'assessmentTemplate' => $assessmentTemplate,
                        'aspect' => $aspect2,
                    ]
                ),
                [
                    'aspect_name' => 'Keterampilan Baru',
                    'weight' => 50,
                ]
            );

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'weight' => 'Total bobot tidak boleh melebihi 100%.',
        ]);

        $this->assertDatabaseHas('assessment_aspects', [
            'id' => $aspect2->id,
            'aspect_name' => 'Keterampilan',
            'weight' => 30,
        ]);
    }

    // Path 4: Branch sesuai, aspect sesuai, total bobot valid
    public function test_path_4_update_aspect_success()
    {
        $branch = Branch::create([
            'name' => 'Cabang',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $assessmentTemplate = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template Penilaian',
        ]);

        $aspect = AssessmentAspect::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Pengetahuan',
            'weight' => 50,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'admin.settings.assessments.assessment-template.aspects.update',
                    [
                        'assessmentTemplate' => $assessmentTemplate,
                        'aspect' => $aspect,
                    ]
                ),
                [
                    'aspect_name' => 'Pengetahuan Baru',
                    'weight' => 70,
                ]
            );

        $response->assertRedirect(
            route(
                'admin.settings.assessments.assessment-template.aspects',
                $assessmentTemplate
            )
        );

        $response->assertSessionHas(
            'status',
            'Aspek berhasil diperbarui.'
        );

        $this->assertDatabaseHas('assessment_aspects', [
            'id' => $aspect->id,
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Pengetahuan Baru',
            'weight' => 70,
        ]);
    }
}