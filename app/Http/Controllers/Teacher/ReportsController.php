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

        $groups = collect([$teacher->group])->sortBy('name')->values();
        $groupIds = $groups->pluck('id');
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();
        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();

        $selectedPeriodId = $request->integer('period_id')
            ?: $activePeriod?->id;

        if ($selectedPeriodId) {

            $students = Student::where('branch_id', $branchId)
                ->whereIn('group_id', $groupIds)
                ->get();

            foreach ($students as $student) {

                Report::firstOrCreate(
                    [
                        'branch_id' => $branchId,
                        'student_id' => $student->id,
                        'period_id' => $selectedPeriodId,
                    ],
                    [
                        'homeroom_teacher_id' => $teacher->id,
                        'report_date' => now(),
                    ]
                );
            }
        }

        $reports = Report::with(['student.group', 'student.guardian', 'period', 'homeroomTeacher'])
            ->where('branch_id', $branchId)
            ->whereHas('student', function ($student) use ($groupIds) {
                $student->whereIn('group_id', $groupIds);
            })
            ->when($request->filled('group_id'), function ($query) use ($request) {
                $query->whereHas('student', function ($student) use ($request) {
                    $student->where('group_id', $request->integer('group_id'));
                });
            })
            ->when($request->filled('period_id'), function ($query) use ($request) {
                $query->where('period_id', $request->integer('period_id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('student', function ($student) use ($request) {
                    $student->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('report_date')
            ->paginate(10)
            ->withQueryString();

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
           
            'genderCounts' => $genderCounts,
            'groups' => $groups,
            'latestAttendance' => $latestAttendance,
            
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

        abort_unless($report->student?->group_id == $teacher->group->id, 403);
        

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
}