<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Period;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $teacher = Auth::user()->teacher;
        $branchId = Auth::user()->branch_id;

        abort_unless($teacher, 403);

        $groupId = $teacher->group_id;

        $activePeriod = Period::where('branch_id', $branchId)
            ->latest()
            ->first();

        $nextSchedule = Schedule::with(['group', 'details.subject'])
            ->where(function ($query) use ($teacher) {
                $query->where('group_id', $teacher->group_id)
                    ->orWhere('all_groups', true);
            })
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date')
            ->first();

        $days = [
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu',
        ];

        $latestAttendance = Attendance::where('teacher_id', $teacher->id)
            ->latest('attendance_date')
            ->first();
        
        return view('teachers.dashboard', [
            'stats' => [
                'My Groups' => $groupId ? 1 : 0,
                'Students' => Student::where('branch_id', $branchId)
                    ->where('group_id', $groupId)
                    ->count(),
                'Schedules' => Schedule::where('branch_id', $branchId)
                    ->where(function ($query) use ($groupId) {
                        $query->where('group_id', $groupId)
                            ->orWhere('all_groups', true);
                    })
                    ->count(),
                'Assessments' => Assessment::where('teacher_id', $teacher->id)
                    ->count(),
            ],
            'teacher' => $teacher,
            'activePeriod' => $activePeriod,
            'nextSchedule' => $nextSchedule,
            'days' => $days,
            'latestAttendance' => $latestAttendance,
        ]);
    }
}