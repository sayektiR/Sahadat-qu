<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentAspect;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyAssessmentAspectWhiteBoxTest extends TestCase
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
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.aspects.destroy',
                    [
                        'assessmentTemplate' => $assessmentTemplate,
                        'aspect' => $aspect,
                    ]
                )
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('assessment_aspects', [
            'id' => $aspect->id,
        ]);
    }

    // Path 2: Aspect bukan milik assessment template
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
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.aspects.destroy',
                    [
                        'assessmentTemplate' => $template1,
                        'aspect' => $aspect,
                    ]
                )
            );

        $response->assertNotFound();

        $this->assertDatabaseHas('assessment_aspects', [
            'id' => $aspect->id,
        ]);
    }

    // Path 3: Branch dan aspect sesuai, penghapusan berhasil
    public function test_path_3_delete_aspect_success()
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
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.aspects.destroy',
                    [
                        'assessmentTemplate' => $assessmentTemplate,
                        'aspect' => $aspect,
                    ]
                )
            );

        $response->assertRedirect();

        $response->assertSessionHas(
            'status',
            'Aspek berhasil dihapus.'
        );

        $this->assertDatabaseMissing('assessment_aspects', [
            'id' => $aspect->id,
        ]);
    }
}