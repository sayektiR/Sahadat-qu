<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateScheduleWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2(True) → 3 → 4 → 5 → 6 → 7
     */
    public function test_path_1_update_schedule_same_branch()
    {
        $branch = Branch::create([
            'name' => 'Cabang A',
            'address' => 'Alamat',
            'phone' => '081111111111',
            'head_name' => 'Ketua',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $group = Group::create([
            'branch_id' => $branch->id,
            'name' => 'Kelompok A',
        ]);

        $period = Period::create([
            'branch_id' => $branch->id,
            'name' => 'Periode',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-20',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'total_meetings' => 10,
            'all_groups' => false,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.schedules.update', $schedule), [
                'group_id' => $group->id,
                'period_id' => $period->id,
                'start_date' => '2026-07-15',
                'end_date' => '2026-07-25',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'total_meetings' => 15,
                'details' => [],
            ]);

        $response->assertRedirect(route('admin.schedules'));

        $response->assertSessionHas(
            'status',
            'Jadwal berhasil diperbarui.'
        );

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'group_id' => $group->id,
            'total_meetings' => 15,
            'start_time' => '09:00',
            'end_time' => '11:00',
        ]);
    }

    /**
     * Path 2
     * 1 → 2(False) → abort(403)
     */
    public function test_path_2_update_schedule_different_branch()
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
            'role' => 'admin',
            'branch_id' => $branchA->id,
        ]);

        $group = Group::create([
            'branch_id' => $branchB->id,
            'name' => 'Kelompok',
        ]);

        $period = Period::create([
            'branch_id' => $branchB->id,
            'name' => 'Periode',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'branch_id' => $branchB->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-20',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'total_meetings' => 10,
            'all_groups' => false,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.schedules.update', $schedule), [
                'group_id' => $group->id,
                'period_id' => $period->id,
                'start_date' => '2026-07-15',
                'end_date' => '2026-07-25',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'total_meetings' => 15,
                'details' => [],
            ]);

        $response->assertForbidden();
    }
}