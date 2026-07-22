<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AssessmentAttribute;
use App\Models\AssessmentTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreAttributeWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    // Path 1: Branch tidak sesuai
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
                    'admin.settings.assessments.assessment-template.attributes.store',
                    $assessmentTemplate
                ),
                [
                    'attribute_name' => 'Hafalan',
                    'attribute_type' => 'text',
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseMissing('assessment_attributes', [
            'assessment_template_id' => $assessmentTemplate->id,
        ]);
    }

    // Path 2: Branch sesuai dan berhasil menambahkan atribut
    public function test_path_2_branch_match_and_store_success()
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
                    'admin.settings.assessments.assessment-template.attributes.store',
                    $assessmentTemplate
                ),
                [
                    'attribute_name' => 'Hafalan',
                    'attribute_type' => 'text',
                ]
            );

        $response->assertRedirect();

        $response->assertSessionHas(
            'status',
            'Atribut berhasil ditambahkan.'
        );

        $this->assertDatabaseHas('assessment_attributes', [
            'assessment_template_id' => $assessmentTemplate->id,
            'attribute_name' => 'Hafalan',
            'attribute_type' => 'text',
        ]);
    }
}