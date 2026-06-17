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

        $lessonScores = Assessment::with(['student', 'lessonAssessment.subject', 'memorizationAssessment'])
            ->where('branch_id', $branchId)
            ->whereIn('student_id', $studentIds)
            ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
            ->orderByDesc('assessment_date')
            ->take(5)
            ->get();

        $latestAttendance = Attendance::with(['group', 'teacher', 'details'])
            ->where('branch_id', $branchId)
            ->whereIn('group_id', $students->pluck('group_id'))
            ->latest('attendance_date')
            ->latest('id')
            ->first();

        $attendanceSummary = $latestAttendance?->details
            ->whereIn('student_id', $studentIds)
            ->countBy('status') ?? collect();

        $upcomingSchedules = Schedule::with(['group', 'period'])
            ->where('branch_id', $branchId)
            ->whereIn('group_id', $students->pluck('group_id'))
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        return view('guardians.dashboard', [
            'activePeriod' => $activePeriod,
            'attendanceSummary' => $attendanceSummary,
            'latestAttendance' => $latestAttendance,
            'lessonScores' => $lessonScores,
            'periods' => $periods,
            'reports' => $reports,
            'stats' => [
                'Students' => $students->count(),
                'Reports' => $reports->count(),
                'Assessments' => $lessonScores->count(),
            ],
            'upcomingSchedules' => $upcomingSchedules,
        ]);
    }
}