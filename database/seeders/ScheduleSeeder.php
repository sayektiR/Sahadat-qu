<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $bojonegoro = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();
        $period = Period::where('branch_id', $bojonegoro->id)->where('name', '2026/2027 Ganjil')->firstOrFail();
        $group = Group::where('branch_id', $bojonegoro->id)->where('name', 'Kelas B Tahfidz')->firstOrFail();
        $subjects = Subject::where('branch_id', $bojonegoro->id)->get()->keyBy('name');

        $schedule = Schedule::updateOrCreate([
            'branch_id' => $bojonegoro->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
        ], [
            'branch_id' => $bojonegoro->id,
            'group_id' => $group->id,
            'period_id' => $period->id,
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'start_time' => '15:30:00',
            'end_time' => '17:30:00',
            'total_meetings' => 48,
        ]);

        $schedule->details()->delete();

        $scheduleMap = [
            'Senin' => ['Tajwid', 'Al-Qur\'an'],
            'Selasa' => ['Hadist', 'Al-Qur\'an', 'Kaligrafi'],
            'Kamis' => ['Aqidah', 'Hafalan'],
            'Sabtu' => ['Ujian'],
        ];

        foreach ($scheduleMap as $day => $items) {
            foreach ($items as $order => $item) {
                $schedule->details()->create([
                    'day' => $day,
                    'subject_id' => $subjects[$item]->id ?? null,
                    'material_name' => $subjects->has($item) ? null : $item,
                    'order_number' => $order + 1,
                ]);
            }
        }
    }
}
