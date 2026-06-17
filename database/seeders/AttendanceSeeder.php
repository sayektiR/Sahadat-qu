<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();
        $period = Period::where('branch_id', $branch->id)->where('name', '2026/2027 Ganjil')->firstOrFail();
        $teachers = Teacher::where('branch_id', $branch->id)->orderBy('id')->get();

        Group::with('students')
            ->where('branch_id', $branch->id)
            ->orderBy('id')
            ->get()
            ->each(function (Group $group, int $index) use ($branch, $period, $teachers): void {
                $teacher = $teachers[$index % $teachers->count()];
                $schedule = Schedule::where('branch_id', $branch->id)->where('group_id', $group->id)->first();

                $attendance = Attendance::updateOrCreate([
                    'branch_id' => $branch->id,
                    'group_id' => $group->id,
                    'attendance_date' => '2026-07-06',
                    'meeting_number' => 1,
                ], [
                    'teacher_id' => $teacher->id,
                    'schedule_id' => $schedule?->id,
                    'period_id' => $period->id,
                    'start_time' => '15:30:00',
                    'end_time' => '17:30:00',
                ]);

                $attendance->details()->delete();

                $group->students->values()->each(function ($student, int $studentIndex) use ($attendance): void {
                    $status = match ($studentIndex % 6) {
                        3 => 'izin',
                        4 => 'sakit',
                        default => 'hadir',
                    };

                    $attendance->details()->create([
                        'student_id' => $student->id,
                        'status' => $status,
                        'note' => $status === 'hadir' ? null : 'Data presensi awal',
                    ]);
                });
            });
    }
}
