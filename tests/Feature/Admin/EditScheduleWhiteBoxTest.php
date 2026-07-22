<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditScheduleWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Path 1
     * 1 → 2(True) → 3
     */
    public function test_path_1_schedule_same_branch()
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
            'name' => 'Periode 1',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'branch_id' => $branch->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'total_meetings' => 12,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.schedules.edit', $schedule));

        $response->assertOk();

        $response->assertViewHas('mode', 'edit');
    }

    /**
     * Path 2
     * 1 → 2(False) → abort(403)
     */
    public function test_path_2_schedule_different_branch()
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
            'name' => 'Kelompok B',
        ]);

        $period = Period::create([
            'branch_id' => $branchB->id,
            'name' => 'Periode',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true,
        ]);

        $schedule = Schedule::create([
            'branch_id' => $branchB->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'total_meetings' => 12,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.schedules.edit', $schedule));

        $response->assertForbidden();
    }
}