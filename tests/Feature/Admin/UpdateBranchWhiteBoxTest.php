<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Branch;

class UpdateBranchWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1:
     * Branch tidak ditemukan
     *
     * Jalur:
     * 1 → 2 → 3 (False) → 403
     */
    public function test_path_1_branch_not_found()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.branch.update'), [
                'name' => 'Cabang Baru',
                'address' => 'Alamat Baru',
                'phone' => '08123456789',
                'head_name' => 'Ketua Baru',
            ]);

        $response->assertForbidden();
    }

    /**
     * Path 2:
     * Branch ditemukan dan berhasil diperbarui
     *
     * Jalur:
     * 1 → 2 → 3 (True) → 4 → 5 → 6 → 7
     */
    public function test_path_2_update_branch_success()
    {
        $branch = Branch::create([
            'name' => 'Cabang Lama',
            'address' => 'Alamat Lama',
            'phone' => '08123456789',
            'head_name' => 'Ketua Lama',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('admin.settings.branch.update'), [
                'name' => 'Cabang Baru',
                'address' => 'Alamat Baru',
                'phone' => '08987654321',
                'head_name' => 'Ketua Baru',
            ]);

        $response->assertRedirect(route('admin.settings'));

        $response->assertSessionHas(
            'status',
            'Pengaturan cabang berhasil diperbarui.'
        );

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Cabang Baru',
            'address' => 'Alamat Baru',
            'phone' => '08987654321',
            'head_name' => 'Ketua Baru',
        ]);
    }
}