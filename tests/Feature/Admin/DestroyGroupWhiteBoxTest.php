<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyGroupWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Independent Path 1
     * 1 → 2(True) → 3(True) → return
     */
    public function test_path_1_group_has_students()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        Student::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'guardian_id' => null,
            'name' => 'Santri',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.groups'))
            ->delete(route('admin.groups.destroy', $group));

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }

    /**
     * Independent Path 2
     * 1 → 2(True) → 3(False) → 4 → 5
     */
    public function test_path_2_group_without_relation()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '08123',
            'head_name' => 'Ketua',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup A',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.groups.destroy', $group));

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHas(
            'status',
            'Kelompok berhasil dihapus.'
        );

        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

    /**
     * Independent Path 3
     * 1 → 2(False) → abort(403)
     */
    public function test_path_3_group_different_branch()
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat A',
            'phone' => '08111',
            'head_name' => 'Ketua A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
            'address' => 'Alamat B',
            'phone' => '08222',
            'head_name' => 'Ketua B',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branchA->id,
            'role' => 'admin',
        ]);

        $group = Group::create([
            'branch_id' => $branchB->id,
            'name' => 'Grup B',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.groups.destroy', $group));

        $response->assertForbidden();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }
}