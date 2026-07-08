<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Period;
use App\Models\Report;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $guardian = Auth::user()->guardian;
        $branchId = Auth::user()->branch_id;

        abort_unless($guardian, 403);

        $studentIds = $guardian->students->pluck('id');
        $students = $guardian->students()->with('group')->get();

        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $reports = Report::with(['student.group', 'period'])
            ->where('branch_id', $branchId)
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('report_date')
            ->take(5)
            ->get();

        $assessments = Assessment::with([
            'student',
            'template',
            'scorings.aspect',
            'attributeValues.attribute',
        ])
            ->where('branch_id', $branchId)
            ->whereIn('student_id', $studentIds)
            ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
            ->latest('assessment_date')
            ->take(5)
            ->get();

        $latestAttendance = Attendance::with(['group', 'teacher', 'details'])
            ->where('branch_id', $branchId)
            ->whereHas('details', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })
            ->latest('attendance_date')
            ->latest('id')
            ->first();

        $attendanceSummary = $latestAttendance?->details
            ->whereIn('student_id', $studentIds)
            ->countBy('status') ?? collect();

       $groupIds = $students->pluck('group_id')->filter();

        $upcomingSchedules = Schedule::with(['group', 'period'])
            ->where('branch_id', $branchId)
            ->where(function ($query) use ($groupIds) {
                $query->where('all_groups', true)
                    ->orWhereIn('group_id', $groupIds);
            })
            ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
            ->whereDate('end_date', '>=', today())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        return view('guardians.dashboard', [
            'activePeriod' => $activePeriod,
            'attendanceSummary' => $attendanceSummary,
            'latestAttendance' => $latestAttendance,
            'assessments' => $assessments,
            'periods' => $periods,
            'reports' => $reports,
            'stats' => [
                'Students' => $students->count(),
                'Reports' => Report::whereIn('student_id', $studentIds)->count(),
                'Assessments' => Assessment::whereIn('student_id', $studentIds)->count(),
            ],
            'upcomingSchedules' => $upcomingSchedules,
        ]);
    }
}