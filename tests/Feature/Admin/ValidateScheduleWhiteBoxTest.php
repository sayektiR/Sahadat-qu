<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidateScheduleWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $branch = Branch::create([
            'name' => 'Cabang',
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
            'name' => 'Semester Ganjil',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        return compact('admin','group','period');
    }

    /**
     * Path 1
     * Valid
     */
    public function test_path_1_valid_schedule()
    {
        extract($this->setupData());

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.schedules.store'),[
                'group_id'=>$group->id,
                'period_id'=>$period->id,
                'start_date'=>'2026-07-10',
                'end_date'=>'2026-07-20',
                'start_time'=>'08:00',
                'end_time'=>'10:00',
                'total_meetings'=>10,
            ]);

        $response->assertRedirect(route('admin.schedules'));
    }

    /**
     * Path 2
     * group kosong
     */
    public function test_path_2_group_required()
    {
        extract($this->setupData());

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.schedules.create'))
            ->post(route('admin.schedules.store'),[
                'period_id'=>$period->id,
                'start_date'=>'2026-07-10',
                'end_date'=>'2026-07-20',
                'start_time'=>'08:00',
                'end_time'=>'10:00',
                'total_meetings'=>10,
            ]);

        $response->assertRedirect(route('admin.schedules.create'));

        $response->assertSessionHasErrors('group_id');
    }

    /**
     * Path 3
     * start_date di luar periode
     */
    public function test_path_3_start_date_outside_period()
    {
        extract($this->setupData());

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.schedules.create'))
            ->post(route('admin.schedules.store'),[
                'group_id'=>$group->id,
                'period_id'=>$period->id,
                'start_date'=>'2026-06-20',
                'end_date'=>'2026-07-20',
                'start_time'=>'08:00',
                'end_time'=>'10:00',
                'total_meetings'=>10,
            ]);

        $response->assertRedirect(route('admin.schedules.create'));

        $response->assertSessionHasErrors('start_date');
    }

    /**
     * Path 4
     * end_date di luar periode
     */
    public function test_path_4_end_date_outside_period()
    {
        extract($this->setupData());

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.schedules.create'))
            ->post(route('admin.schedules.store'),[
                'group_id'=>$group->id,
                'period_id'=>$period->id,
                'start_date'=>'2026-07-10',
                'end_date'=>'2027-01-10',
                'start_time'=>'08:00',
                'end_time'=>'10:00',
                'total_meetings'=>10,
            ]);

        $response->assertRedirect(route('admin.schedules.create'));

        $response->assertSessionHasErrors('end_date');
    }

}