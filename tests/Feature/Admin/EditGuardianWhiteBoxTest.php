<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditGuardianWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Independent Path 1
     * 1 → 2(True) → 3 → 4
     */
    public function test_path_1_guardian_same_branch()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat Cabang A',
            'phone' => '08123456789',
            'head_name' => 'Ketua Cabang A',
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
            'name' => 'Guardian Test',
            'phone' => '081111111111',
            'address' => 'Alamat',
            'relation' => 'Ayah',
        ]);

        $guardian->load('user');

        $guardian = Guardian::with('user')->findOrFail($guardian->id);

        $response = $this
            ->withoutExceptionHandling()
            ->actingAs($admin, 'web')
            ->get(route('admin.guardians.edit', $guardian));


        $response->assertViewIs('admin.guardians.form');

        $response->assertViewHas('guardian', $guardian);

        $response->assertViewHas('mode', 'edit');
    }

    /**
     * Independent Path 2
     * 1 → 2(False) → abort(403)
     */
    public function test_path_2_guardian_different_branch()
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

        $guardianUser = User::factory()->create([
            'branch_id' => $branchB->id,
            'role' => 'guardian',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian B',
            'phone' => '082222222222',
            'address' => 'Alamat Guardian B',
            'relation' => 'Ibu',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.guardians.edit', $guardian));

        $response->assertForbidden();
    }
}