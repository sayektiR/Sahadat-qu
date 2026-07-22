<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentAspect;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreAssessmentAspectWhiteBoxTest extends TestCase
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

        $response = $this
            ->actingAs($user)
            ->post(
                route(
                    'admin.settings.assessments.assessment-template.aspects.store',
                    $assessmentTemplate
                ),
                [
                    'aspect_name' => 'Pengetahuan',
                    'weight' => 50,
                ]
            );

        $response->assertForbidden();
    }

    // Path 2: Branch sesuai, total bobot tidak melebihi 100
    public function test_path_2_branch_match_and_weight_valid()
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

        $response = $this
            ->actingAs($user)
            ->post(
                route(
                    'admin.settings.assessments.assessment-template.aspects.store',
                    $assessmentTemplate
                ),
                [
                    'aspect_name' => 'Pengetahuan',
                    'weight' => 50,
                ]
            );

        $response->assertRedirect();

        $response->assertSessionHas(
            'status',
            'Aspek penilaian berhasil ditambahkan.'
        );

        $this->assertDatabaseHas('assessment_aspects', [
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Pengetahuan',
            'weight' => 50,
        ]);
    }

    // Path 3: Total bobot melebihi 100
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

        AssessmentAspect::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Pengetahuan',
            'weight' => 80,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(
                route(
                    'admin.settings.assessments.assessment-template.aspects.store',
                    $assessmentTemplate
                ),
                [
                    'aspect_name' => 'Keterampilan',
                    'weight' => 30,
                ]
            );

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'weight' => 'Total bobot tidak boleh melebihi 100%.',
        ]);

        $this->assertDatabaseMissing('assessment_aspects', [
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => 'Keterampilan',
        ]);
    }
}