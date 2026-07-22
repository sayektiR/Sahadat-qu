<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateStudentWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2(True) → 3 → 4 → 5 → 6
     */
    public function test_path_1_update_student_same_branch()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $guardianUser = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian',
        ]);

        $student = Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'guardian_id' => $guardian->id,
            'nis' => 'SQ-0001',
            'nik' => '1234567890123456',
            'name' => 'Santri Lama',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.students.update', $student), [
                'group_id' => $group->id,
                'guardian_id' => $guardian->id,
                'nis' => 'SQ-0001',
                'nik' => '1234567890123456',
                'name' => 'Santri Baru',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.students'));

        $student->refresh();

        $this->assertEquals('Santri Baru', $student->name);
    }

    /**
     * Path 2
     * 1 → 2(False) → abort(403)
     */
    public function test_path_2_update_student_different_branch()
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
        ]);

        $group = Group::create([
            'branch_id' => $branchB->id,
            'name' => 'Grup B',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branchA->id,
            'role' => 'admin',
        ]);

        $guardianUser = User::factory()->create([
            'branch_id' => $branchB->id,
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian',
        ]);

        $student = Student::create([
            'branch_id' => $branchB->id,
            'group_id' => $group->id,
            'guardian_id' => $guardian->id,
            'nis' => 'SQ-0002',
            'nik' => '1111222233334444',
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.students.update', $student), [
                'group_id' => $group->id,
                'guardian_id' => $guardian->id,
                'nis' => 'SQ-0002',
                'nik' => '1111222233334444',
                'name' => 'Santri Baru',
                'status' => 'active',
            ]);

        $response->assertForbidden();
    }
}