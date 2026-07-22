<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyAssessmentTemplateWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

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
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.destroy',
                    $assessmentTemplate
                )
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('assessment_templates', [
            'id' => $assessmentTemplate->id,
            'name' => 'Template Penilaian',
        ]);
    }

    public function test_path_2_delete_success()
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
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.destroy',
                    $assessmentTemplate
                )
            );

        $response->assertRedirect(
            route('admin.settings.assessments.assessment-template')
        );

        $response->assertSessionHas(
            'status',
            'Penilaian berhasil dihapus.'
        );

        $this->assertDatabaseMissing('assessment_templates', [
            'id' => $assessmentTemplate->id,
        ]);
    }
}