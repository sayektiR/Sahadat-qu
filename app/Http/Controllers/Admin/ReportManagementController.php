<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AttendanceDetail;
use App\Models\Group;
use App\Models\Period;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportManagementController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;
        $groups = Group::where('branch_id', $branchId)->orderBy('name')->get();
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $reports = Report::with(['student.group', 'student.guardian', 'period', 'homeroomTeacher'])
            ->where('branch_id', $branchId)
            ->when($request->filled('group_id'), function ($query) use ($request) {
                $query->whereHas('student', fn ($student) => $student->where('group_id', $request->integer('group_id')));
            })
            ->when($request->filled('period_id'), fn ($query) => $query->where('period_id', $request->integer('period_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->whereHas('student', fn ($student) => $student->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('student.guardian', fn ($guardian) => $guardian->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('student.group', fn ($group) => $group->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('report_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact('groups', 'periods', 'reports'));
    }

    public function show(Report $report): View
    {
        $this->ensureReportBranch($report);

        $report->load(['branch', 'student.group', 'student.guardian', 'period', 'homeroomTeacher']);

        $assessments = Assessment::with([
            'template',
            'scorings.aspect',
            'attributeValues.attribute',
        ])
            ->where('branch_id', $report->branch_id)
            ->where('student_id', $report->student_id)
            ->where('period_id', $report->period_id)
            ->orderBy('assessment_date')
            ->get();


        $attendanceSummary = AttendanceDetail::where('student_id', $report->student_id)
            ->whereHas('attendance', fn ($attendance) => $attendance->where('period_id', $report->period_id))
            ->get()
            ->countBy('status');

        return view('pdf.template-raport', [
            'attendanceSummary' => $attendanceSummary,
            'assessments' => $assessments,
            'report' => $report,
        ]);
    }

    private function ensureReportBranch(Report $report): void
    {
        abort_unless($report->branch_id === Auth::user()->branch_id, 403);
    }
}
