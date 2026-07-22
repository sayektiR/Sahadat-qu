<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyGuardianWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2(True) → 3(True)
     */
    public function test_path_1_guardian_has_students()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
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

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        Student::create([
            'branch_id'   => $branch->id,
            'guardian_id' => $guardian->id,
            'group_id'    => $group->id,
            'name'        => 'Santri',
            'status'      => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.guardians.destroy', $guardian));

        $response->assertSessionHasErrors('guardian');

        $this->assertDatabaseHas('guardians', [
            'id' => $guardian->id,
        ]);
    }

    /**
     * Path 2
     * 1 → 2(True) → 3(False) → 4 → 5
     */
    public function test_path_2_guardian_without_students()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
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

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.guardians.destroy', $guardian));

        $response->assertRedirect(route('admin.guardians'));

        $this->assertDatabaseMissing('users', [
            'id' => $guardianUser->id,
        ]);
    }

    /**
     * Path 3
     * 1 → 2(False) → abort(403)
     */
    public function test_path_3_guardian_different_branch()
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
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

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.guardians.destroy', $guardian));

        $response->assertForbidden();
    }
}