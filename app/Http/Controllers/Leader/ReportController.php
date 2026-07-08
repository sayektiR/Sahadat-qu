<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AttendanceDetail;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::orderBy('name')->get();
        $groups = Group::when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderBy('name')
            ->get();
        $periods = Period::when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->get();

        $query = Report::with(['branch', 'student.branch', 'student.group.branch', 'student.guardian', 'period', 'homeroomTeacher'])
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('group_id'), fn ($query) => $query->whereHas('student', fn ($s) => $s->where('group_id', $request->integer('group_id'))))
            ->when($request->filled('period_id'), fn ($query) => $query->where('period_id', $request->integer('period_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('student.guardian', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('branch', fn ($branch) => $branch->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('student.group', fn ($g) => $g->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('report_date');

        $reports = $query->paginate(10)->withQueryString();

        return view('leader.reports.index', [
            'reports' => $reports,
            'branches' => $branches,
            'groups' => $groups,
            'periods' => $periods,
        ]);
    }

    public function show(Report $report): View
{
    $report->load([
        'branch',
        'student.branch',
        'student.group',
        'student.guardian',
        'period',
        'homeroomTeacher'
    ]);

    $assessments = Assessment::with([
        'template',
        'scorings.aspect'
    ])
    ->where('branch_id', $report->branch_id)
    ->where('student_id', $report->student_id)
    ->where('period_id', $report->period_id)
    ->orderBy('assessment_date')
    ->get();

    // Attendance
    $attendanceDetails = AttendanceDetail::with(['attendance.group', 'attendance.period'])
        ->whereHas('attendance', function ($query) use ($report) {
            $query->where('branch_id', $report->branch_id)
                ->where('period_id', $report->period_id);
        })
        ->where('student_id', $report->student_id)
        ->get();

    $attendanceSummary = $attendanceDetails->countBy('status');

    return view('leader.reports.show', [
        'attendanceDetails' => $attendanceDetails,
        'attendanceSummary' => $attendanceSummary,
        'assessments' => $assessments,
        'report' => $report,
    ]);
}
}
