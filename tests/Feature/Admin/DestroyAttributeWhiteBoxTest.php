<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentAttribute;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyAttributeWhiteBoxTest extends TestCase
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

        $attribute = AssessmentAttribute::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'attribute_name' => 'Hafalan',
            'attribute_type' => 'text',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.attributes.destroy',
                    [
                        'assessmentTemplate' => $assessmentTemplate->id,
                        'attribute' => $attribute->id,
                    ]
                )
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('assessment_attributes', [
            'id' => $attribute->id,
            'attribute_name' => 'Hafalan',
        ]);
    }

    // Path 2: Branch sesuai, tetapi attribute bukan milik template
    public function test_path_2_attribute_not_belong_to_template()
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

        $attribute = AssessmentAttribute::create([
            'assessment_template_id' => $template2->id,
            'attribute_name' => 'Hafalan',
            'attribute_type' => 'text',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.attributes.destroy',
                    [
                        'assessmentTemplate' => $template1->id,
                        'attribute' => $attribute->id,
                    ]
                )
            );

        $response->assertNotFound();

        $this->assertDatabaseHas('assessment_attributes', [
            'id' => $attribute->id,
            'assessment_template_id' => $template2->id,
            'attribute_name' => 'Hafalan',
        ]);
    }

    // Path 3: Branch sesuai dan attribute milik template
    public function test_path_3_delete_attribute_success()
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

        $attribute = AssessmentAttribute::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'attribute_name' => 'Hafalan',
            'attribute_type' => 'text',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route(
                    'admin.settings.assessments.assessment-template.attributes.destroy',
                    [
                        'assessmentTemplate' => $assessmentTemplate->id,
                        'attribute' => $attribute->id,
                    ]
                )
            );

        $response->assertRedirect();

        $response->assertSessionHas(
            'status',
            'Atribut berhasil dihapus.'
        );

        $this->assertDatabaseMissing('assessment_attributes', [
            'id' => $attribute->id,
        ]);
    }
}