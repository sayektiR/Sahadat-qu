<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private array $statuses = ['hadir', 'izin', 'sakit', 'alpha'];

    public function index(Request $request): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

       $groups = collect();

        if ($teacher->group) {
            $groups->push($teacher->group);
        }

        $selectedGroupId = $teacher->group_id;
        $activePeriod = Period::where('branch_id', Auth::user()->branch_id)->where('is_active', true)->first();
        $schedule = $selectedGroupId
            ? Schedule::where('branch_id', Auth::user()->branch_id)
                ->where('group_id', $selectedGroupId)
                ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
                ->latest('start_date')
                ->first()
            : null;

        $students = $selectedGroupId
            ? Student::with('group')
                ->where('branch_id', Auth::user()->branch_id)
                ->where('group_id', $selectedGroupId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get()
            : collect();

        $attendances = Attendance::with(['group', 'details.student'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('teacher_id', $teacher->id)
            ->where('group_id', $selectedGroupId)
            ->when($request->filled('history_group_id'), fn ($query) => $query->where('group_id', $request->integer('history_group_id')))
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        $latestAttendance = Attendance::with(['group', 'details.student.group'])
            ->when(
                $request->attendance_id,
                fn ($q) => $q->where('id', $request->attendance_id),
                fn ($q) => $q->where('teacher_id', $teacher->id)
                            ->latest('attendance_date')
                            ->latest('id')
            )
            ->first();

        $nextMeeting = 1;

        if ($selectedGroupId && $activePeriod) {

            $lastAttendance = Attendance::where('group_id', $selectedGroupId)
                ->where('period_id', $activePeriod->id)
                ->latest('attendance_date')
                ->latest('meeting_number')
                ->first();

            $nextMeeting = $lastAttendance
                ? $lastAttendance->meeting_number + 1
                : 1;
        }

        return view('teachers.attendance.index', [
            'activePeriod' => $activePeriod,
            'attendances' => $attendances,
            'groups' => $groups,
            'latestAttendance' => $latestAttendance,
            'schedule' => $schedule,
            'selectedGroupId' => $selectedGroupId,
            'statuses' => $this->statuses,
            'students' => $students,
            'teacher' => $teacher,
            'nextMeeting' => $nextMeeting,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $groupIds = collect([$teacher->group_id]);
        $branchId = Auth::user()->branch_id;

        $data = $request->validate([
            'group_id' => ['required', Rule::in($groupIds->all())],
            'schedule_id' => ['nullable', Rule::exists('schedules', 'id')->where('branch_id', $branchId)],
            'period_id' => ['required', Rule::exists('periods', 'id')->where('branch_id', $branchId)],
            'attendance_date' => ['required', 'date'],
            'meeting_number' => ['required', 'integer', 'min:1'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'students' => ['required', 'array', 'min:1'],
            'students.*' => ['array'],
            'students.*.status' => ['required', Rule::in($this->statuses)],
            'students.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $studentIds = Student::where('branch_id', $branchId)
            ->where('group_id', $data['group_id'])
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        $attendanceId = null;

        DB::transaction(function () use ($data, $teacher, $branchId, $studentIds, &$attendanceId): void {
            $attendance = Attendance::updateOrCreate([
                'branch_id' => $branchId,
                'group_id' => $data['group_id'],
                'teacher_id' => $teacher->id,
                'attendance_date' => $data['attendance_date'],
                'meeting_number' => $data['meeting_number'],
            ], [
                'schedule_id' => $data['schedule_id'] ?? null,
                'period_id' => $data['period_id'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
            ]);
            
            $attendanceId = $attendance->id;

            $attendance->details()->delete();

            foreach ($data['students'] as $studentId => $detail) {
                if (! in_array((int) $studentId, $studentIds, true)) {
                    continue;
                }

                $attendance->details()->create([
                    'student_id' => $studentId,
                    'status' => $detail['status'],
                    'note' => $detail['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('teachers.attendance', [
            'group_id' => $data['group_id'],
            'tab' => 'latest',
            'attendance_id' => $attendanceId,
        ])->with('status', 'Presensi berhasil disimpan.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher && $attendance->teacher_id === $teacher->id, 403);

        $attendance->delete();

        return redirect()->route('teachers.attendance')->with('status', 'Presensi berhasil dihapus.');
    }
}
