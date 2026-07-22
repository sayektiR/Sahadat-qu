<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Test',
        ]);

        $this->admin = User::factory()->create([
            'branch_id' => $this->branch->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function createPeriod(array $override = []): Period
    {
        return Period::create(array_merge([
            'branch_id' => $this->branch->id,
            'name' => 'Periode Lama ' . uniqid(),
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ], $override));
    }

    private function validPeriodData(array $override = []): array
    {
        return array_merge([
            'name' => 'Periode Baru ' . uniqid(),
            'academic_year' => '2025/2026',
            'semester' => 'Ganjil',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ], $override);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-01
    | Semua data valid
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_01_store_period_all_valid_data_succeeds(): void
    {
        $data = $this->validPeriodData();

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));

        $this->assertDatabaseHas('periods', [
            'branch_id' => $this->branch->id,
            'name' => $data['name'],
            'academic_year' => $data['academic_year'],
            'semester' => $data['semester'],
            'is_active' => 0,
        ]);

        $period = Period::where('name', $data['name'])->first();

        $this->assertNotNull($period);

        $this->assertEquals(
            '2026-01-01',
            $period->start_date->format('Y-m-d')
        );

        $this->assertEquals(
            '2026-06-30',
            $period->end_date->format('Y-m-d')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-02
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_02_store_period_empty_name_fails(): void
    {
        $data = $this->validPeriodData([
            'name' => '',
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-03
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_03_store_period_duplicate_name_same_branch_fails(): void
    {
        $this->createPeriod([
            'name' => 'Periode Sama',
        ]);

        $data = $this->validPeriodData([
            'name' => 'Periode Sama',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-04
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_04_store_period_empty_academic_year_fails(): void
    {
        $data = $this->validPeriodData([
            'academic_year' => '',
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('academic_year');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-05
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_05_store_period_empty_semester_fails(): void
    {
        $data = $this->validPeriodData([
            'semester' => null,
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('semester');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-06
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_06_store_period_empty_start_date_fails(): void
    {
        $data = $this->validPeriodData([
            'start_date' => null,
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors([
            'start_date' => 'The start date field is required.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-07
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_07_store_period_empty_end_date_fails(): void
    {
        $data = $this->validPeriodData([
            'end_date' => null,
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors([
            'end_date' => 'The end date field is required.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-08
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_08_store_period_end_date_before_start_date_fails(): void
    {
        $data = $this->validPeriodData([
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-09',
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('end_date');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-09
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_09_store_period_overlapping_date_fails(): void
    {
        $this->createPeriod([
            'name' => 'Periode Lama',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
        ]);

        $data = $this->validPeriodData([
            'name' => 'Periode Baru',
            'start_date' => '2025-10-01',
            'end_date' => '2026-03-31',
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('start_date');

        $this->assertDatabaseMissing('periods', [
            'name' => 'Periode Baru',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-10
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_10_store_active_period_deactivates_previous_period(): void
    {
        $oldPeriod = $this->createPeriod([
            'name' => 'Periode Lama',
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $data = $this->validPeriodData([
            'name' => 'Periode Baru',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));

        $this->assertDatabaseHas('periods', [
            'id' => $oldPeriod->id,
            'is_active' => 0,
        ]);

        $this->assertDatabaseHas('periods', [
            'name' => 'Periode Baru',
            'is_active' => 1,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-11
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_11_store_period_name_254_chars_succeeds(): void
    {
        $data = $this->validPeriodData([
            'name' => str_repeat('A', 254),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-12
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_12_store_period_name_255_chars_succeeds(): void
    {
        $data = $this->validPeriodData([
            'name' => str_repeat('A', 255),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-13
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_13_store_period_name_256_chars_fails(): void
    {
        $data = $this->validPeriodData([
            'name' => str_repeat('A', 256),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-14
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_14_store_period_academic_year_19_chars_succeeds(): void
    {
        $data = $this->validPeriodData([
            'academic_year' => str_repeat('A', 19),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-15
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_15_store_period_academic_year_20_chars_succeeds(): void
    {
        $data = $this->validPeriodData([
            'academic_year' => str_repeat('A', 20),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-16
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_16_store_period_academic_year_21_chars_fails(): void
    {
        $data = $this->validPeriodData([
            'academic_year' => str_repeat('A', 21),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('academic_year');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-17
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_17_store_period_semester_49_chars_succeeds(): void
    {
        $data = $this->validPeriodData([
            'semester' => str_repeat('A', 49),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-18
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_18_store_period_semester_50_chars_succeeds(): void
    {
        $data = $this->validPeriodData([
            'semester' => str_repeat('A', 50),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertRedirect(route('admin.periods'));
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-19
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_19_store_period_semester_51_chars_fails(): void
    {
        $data = $this->validPeriodData([
            'semester' => str_repeat('A', 51),
        ]);

        $response = $this->post(
            route('admin.periods.store'),
            $data
        );

        $response->assertSessionHasErrors('semester');
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-20
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_20_destroy_period_without_relation_succeeds(): void
    {
        $period = $this->createPeriod();

        $response = $this->delete(
            route('admin.periods.destroy', $period)
        );

        $response->assertRedirect(route('admin.periods'));

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('periods', [
            'id' => $period->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TC-PR-21
    |--------------------------------------------------------------------------
    */

    public function test_tc_pr_21_destroy_period_with_schedule_fails(): void
    {
        $period = $this->createPeriod();

        $group = Group::create([
            'branch_id' => $this->branch->id,
            'name' => 'Kelompok Test',
        ]);

        Schedule::create([
            'branch_id' => $this->branch->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'total_meetings' => 16,
        ]);

        $response = $this->delete(
            route('admin.periods.destroy', $period)
        );

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('periods', [
            'id' => $period->id,
        ]);
    }
}