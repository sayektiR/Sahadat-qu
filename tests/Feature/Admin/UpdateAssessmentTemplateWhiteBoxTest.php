<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AssessmentTemplate;

class UpdateAssessmentTemplateWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    public function test_path_1_branch_not_match()
    {
        $branch1 = Branch::create([
            'name' => 'Cabang 1',
            'address' => 'Alamat',
            'phone' => '08111',
            'head_name' => 'Ketua 1',
        ]);

        $branch2 = Branch::create([
            'name' => 'Cabang 2',
            'address' => 'Alamat',
            'phone' => '08222',
            'head_name' => 'Ketua 2',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch1->id,
        ]);

        $template = AssessmentTemplate::create([
            'branch_id' => $branch2->id,
            'name' => 'Template Lama',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'admin.settings.assessments.assessment-template.update',
                    $template
                ),
                [
                    'name' => 'Template Baru',
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('assessment_templates', [
            'id' => $template->id,
            'name' => 'Template Lama',
        ]);
    }

    public function test_path_2_update_success()
    {
        $branch = Branch::create([
            'name' => 'Cabang 1',
            'address' => 'Alamat',
            'phone' => '08111',
            'head_name' => 'Ketua 1',
        ]);

        $template = AssessmentTemplate::create([
            'branch_id' => $branch->id,
            'name' => 'Template Lama',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route(
                    'admin.settings.assessments.assessment-template.update',
                    $template
                ),
                [
                    'name' => 'Template Baru',
                ]
            );

        $response->assertRedirect(
            route('admin.settings.assessments.assessment-template')
        );

        $response->assertSessionHas('status', 'Penilaian berhasil diperbarui.');

        $this->assertDatabaseHas('assessment_templates', [
            'id' => $template->id,
            'name' => 'Template Baru',
        ]);
    }
}