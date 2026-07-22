<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentAspect;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssessmentTemplateAspectsWhiteBoxTest extends TestCase
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
            ->get(
                route(
                    'admin.settings.assessments.assessment-template.aspects',
                    $assessmentTemplate
                )
            );

        $response->assertForbidden();
    }

    // Path 2: Branch sesuai dan berhasil menampilkan aspek
    public function test_path_2_branch_match_show_aspects()
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
            'weight' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(
                route(
                    'admin.settings.assessments.assessment-template.aspects',
                    $assessmentTemplate
                )
            );

        $response->assertOk();

        $response->assertViewIs(
            'admin.settings.assessments.aspect'
        );

        $response->assertViewHas(
            'assessmentTemplate',
            $assessmentTemplate
        );

        $response->assertViewHas('aspects', function ($aspects) use ($aspect) {
            return $aspects->contains($aspect);
        });
    }
}