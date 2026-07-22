<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateGroupWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Independent Path 1
     * 1 → 2(True) → 3 → 4 → 5
     */
    public function test_path_1_update_group_same_branch()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '08123456789',
            'head_name' => 'Ketua',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Grup Lama',
            'description' => 'Deskripsi Lama',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.groups.update', $group), [
                'name' => 'Grup Baru',
                'description' => 'Deskripsi Baru',
            ]);

        $response->assertRedirect(route('admin.groups'));

        $response->assertSessionHas('status', 'Kelompok berhasil diperbarui.');

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Grup Baru',
            'description' => 'Deskripsi Baru',
        ]);
    }

    /**
     * Independent Path 2
     * 1 → 2(False) → abort(403)
     */
    public function test_path_2_update_group_different_branch()
    {
        $branchA = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat A',
            'phone' => '081111111111',
            'head_name' => 'Ketua A',
        ]);

        $branchB = Branch::create([
            'name' => 'Cabang B',
            'address' => 'Alamat B',
            'phone' => '082222222222',
            'head_name' => 'Ketua B',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branchA->id,
            'role' => 'admin',
        ]);

        $group = Group::create([
            'branch_id' => $branchB->id,
            'name' => 'Grup B',
            'description' => 'Deskripsi',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.groups.update', $group), [
                'name' => 'Tidak Berubah',
                'description' => 'Tidak Berubah',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Grup B',
        ]);
    }
}