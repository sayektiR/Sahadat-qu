<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $branchId = Auth::user()->branch_id;
        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first()
            ?: Period::where('branch_id', $branchId)->latest('start_date')->first();

        $students = Student::with('group')->where('branch_id', $branchId)->get();
        $groups = Group::withCount('students')->where('branch_id', $branchId)->orderBy('name')->get();
        $teachers = Teacher::with('groups')->where('branch_id', $branchId)->orderBy('name')->get();
        $latestAttendance = Attendance::with(['group', 'teacher', 'details'])
            ->where('branch_id', $branchId)
            ->latest('attendance_date')
            ->latest('id')
            ->first();
        $latestSchedule = Schedule::with(['group', 'period', 'details.subject'])
            ->where('branch_id', $branchId)
            ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
            ->latest('start_date')
            ->first();

        $months = $activePeriod
            ? collect(CarbonPeriod::create($activePeriod->start_date->copy()->startOfMonth(), '1 month', $activePeriod->end_date->copy()->startOfMonth()))
            : collect();

        $memorizationAssessments = Assessment::with('student')
            ->where('branch_id', $branchId)
            ->where('assessment_type', 'hafalan')
            ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
            ->get();

        $memorizationChart = $months->map(function ($month) use ($memorizationAssessments) {
            $items = $memorizationAssessments->filter(fn ($assessment) => $assessment->assessment_date?->format('Y-m') === $month->format('Y-m'));

            return [
                'label' => $month->translatedFormat('M'),
                'male' => $items->filter(fn ($assessment) => $assessment->student?->gender === 'male')->count(),
                'female' => $items->filter(fn ($assessment) => $assessment->student?->gender === 'female')->count(),
            ];
        });

        $chartMax = max(1, $memorizationChart->flatMap(fn ($item) => [$item['male'], $item['female']])->max() ?? 1);
        $genderCounts = [
            'male' => $students->where('gender', 'male')->count(),
            'female' => $students->where('gender', 'female')->count(),
        ];

        return view('admin.dashboard', [
            'activePeriod' => $activePeriod,
            'chartMax' => $chartMax,
            'genderCounts' => $genderCounts,
            'groups' => $groups,
            'latestAttendance' => $latestAttendance,
            'latestSchedule' => $latestSchedule,
            'memorizationChart' => $memorizationChart,
            'stats' => [
                'students' => $students->count(),
                'teachers' => $teachers->count(),
                'guardians' => User::where('branch_id', $branchId)->where('role', 'guardian')->count(),
                'groups' => $groups->count(),
                'subjects' => Subject::where('branch_id', $branchId)->count(),
            ],
            'teachers' => $teachers,
        ]);
    }
}
