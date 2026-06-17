<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AttendanceDetail;
use App\Models\Period;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $guardian = Auth::user()->guardian;
        $branchId = Auth::user()->branch_id;

        abort_unless($guardian, 403);

        $studentIds = $guardian->students->pluck('id');

        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;

        $reports = Report::with(['student.group', 'student.guardian', 'period', 'homeroomTeacher'])
            ->where('branch_id', $branchId)
            ->whereIn('student_id', $studentIds)
            ->when($selectedPeriodId, fn ($query) => $query->where('period_id', $selectedPeriodId))
            ->orderByDesc('report_date')
            ->paginate(10)
            ->withQueryString();

        return view('guardians.reports.index', [
            'activePeriod' => $activePeriod,
            'periods' => $periods,
            'reports' => $reports,
            'selectedPeriodId' => $selectedPeriodId,
        ]);
    }

    public function show(Report $report): View
    {
        $guardian = Auth::user()->guardian;

        abort_unless($guardian, 403);
        abort_unless($report->branch_id === Auth::user()->branch_id, 403);

        $studentIds = $guardian->students->pluck('id');
        abort_unless(in_array($report->student_id, $studentIds->toArray()), 403);

        $report->load(['branch', 'student.group', 'student.guardian', 'period', 'homeroomTeacher']);

        $lessonAssessments = Assessment::with('lessonAssessment.subject')
            ->where('branch_id', $report->branch_id)
            ->where('student_id', $report->student_id)
            ->where('period_id', $report->period_id)
            ->where('assessment_type', 'materi')
            ->orderBy('assessment_date')
            ->get();

        $memorizationAssessments = Assessment::with('memorizationAssessment')
            ->where('branch_id', $report->branch_id)
            ->where('student_id', $report->student_id)
            ->where('period_id', $report->period_id)
            ->where('assessment_type', 'hafalan')
            ->orderBy('assessment_date')
            ->get();

        $attendanceSummary = AttendanceDetail::where('student_id', $report->student_id)
            ->whereHas('attendance', fn ($attendance) => $attendance->where('period_id', $report->period_id))
            ->get()
            ->countBy('status');

        return view('pdf.template-raport', [
            'attendanceSummary' => $attendanceSummary,
            'lessonAssessments' => $lessonAssessments,
            'memorizationAssessments' => $memorizationAssessments,
            'report' => $report,
        ]);
    }
}