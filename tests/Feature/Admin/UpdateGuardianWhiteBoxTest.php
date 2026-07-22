<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateGuardianWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2(True) → 3 → 4 → 5 → 6 → 7
     */
    public function test_path_1_update_guardian_same_branch()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '081111111111',
            'head_name' => 'Ketua',
        ]);

        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'admin',
        ]);

        $guardianUser = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => 'guardian',
            'email' => 'guardian@test.com',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian Lama',
            'phone' => '081111111111',
            'address' => 'Alamat Lama',
            'relation' => 'Ayah',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.guardians.update', $guardian), [
                'name' => 'Guardian Baru',
                'email' => 'guardianbaru@test.com',
                'phone' => '082222222222',
                'address' => 'Alamat Baru',
                'relation' => 'Ibu',
            ]);

        $response->assertRedirect(route('admin.guardians'));

        $guardian->refresh();
        $guardianUser->refresh();

        $this->assertEquals('Guardian Baru', $guardian->name);
        $this->assertEquals('082222222222', $guardian->phone);
        $this->assertEquals('Alamat Baru', $guardian->address);
        $this->assertEquals('Ibu', $guardian->relation);

        $this->assertEquals('Guardian Baru', $guardianUser->name);
        $this->assertEquals('guardianbaru@test.com', $guardianUser->email);
        $this->assertEquals('082222222222', $guardianUser->phone);
        $this->assertEquals('Alamat Baru', $guardianUser->address);
    }

    /**
     * Path 2
     * 1 → 2(False) → abort(403)
     */
    public function test_path_2_update_guardian_different_branch()
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
            'email' => 'guardian@test.com',
        ]);

        $guardian = Guardian::create([
            'user_id' => $guardianUser->id,
            'name' => 'Guardian',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.guardians.update', $guardian), [
                'name' => 'Guardian Baru',
                'email' => 'baru@test.com',
            ]);

        $response->assertForbidden();
    }
}