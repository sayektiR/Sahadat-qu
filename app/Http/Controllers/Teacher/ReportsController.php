<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Period;
use App\Models\Report;
use App\Models\Student;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user()->teacher;
        $branchId = Auth::user()->branch_id;

        abort_unless($teacher, 403);

        $groups = $teacher->groups->sortBy('name')->values();
        $groupIds = $groups->pluck('id');
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();
        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();

        $reports = Report::with(['student.group', 'student.guardian', 'period', 'homeroomTeacher'])
            ->where('branch_id', $branchId)
            ->whereHas('student', fn ($student) => $student->whereIn('group_id', $groupIds))
            ->when($request->filled('group_id'), fn ($query) => $query->whereHas('student', fn ($student) => $student->where('group_id', $request->integer('group_id'))))
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

        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;

        $months = $activePeriod
            ? collect(CarbonPeriod::create($activePeriod->start_date->copy()->startOfMonth(), '1 month', $activePeriod->end_date->copy()->startOfMonth()))
            : collect();

        $memorizationAssessments = Assessment::with('student')
            ->where('branch_id', $branchId)
            ->where('teacher_id', $teacher->id)
            ->where('assessment_type', 'hafalan')
            ->when($selectedPeriodId, fn ($query) => $query->where('period_id', $selectedPeriodId))
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

        $students = Student::where('branch_id', $branchId)->whereIn('group_id', $groupIds)->get();
        $genderCounts = [
            'male' => $students->where('gender', 'male')->count(),
            'female' => $students->where('gender', 'female')->count(),
        ];

        $latestAttendance = Attendance::with(['group', 'details'])
            ->where('branch_id', $branchId)
            ->where('teacher_id', $teacher->id)
            ->latest('attendance_date')
            ->latest('id')
            ->first();

        $attendanceSummary = $latestAttendance?->details?->countBy('status') ?? collect();

        return view('teachers.reports.index', [
            'activePeriod' => $activePeriod,
            'attendanceSummary' => $attendanceSummary,
            'chartMax' => $chartMax,
            'genderCounts' => $genderCounts,
            'groups' => $groups,
            'latestAttendance' => $latestAttendance,
            'memorizationChart' => $memorizationChart,
            'periods' => $periods,
            'reports' => $reports,
            'selectedPeriodId' => $selectedPeriodId,
            'totalStudents' => $students->count(),
        ]);
    }

    public function show(Report $report): View
    {
        $teacher = Auth::user()->teacher;

        abort_unless($teacher, 403);
        abort_unless($report->branch_id === Auth::user()->branch_id, 403);

        $teacherGroups = $teacher->groups->pluck('id');
        abort_unless(in_array($report->student?->group_id, $teacherGroups->toArray()), 403);

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