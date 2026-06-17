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

        $groups = $teacher->groups()->orderBy('name')->get();
        $groupIds = $groups->pluck('id');
        $requestedGroupId = $request->integer('group_id');
        $selectedGroupId = $groupIds->contains($requestedGroupId) ? $requestedGroupId : $groupIds->first();
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
            ->whereIn('group_id', $groupIds)
            ->when($request->filled('history_group_id'), fn ($query) => $query->where('group_id', $request->integer('history_group_id')))
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        $latestAttendance = Attendance::with(['group', 'details.student.group'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('teacher_id', $teacher->id)
            ->whereIn('group_id', $groupIds)
            ->when($selectedGroupId, fn ($query) => $query->where('group_id', $selectedGroupId))
            ->latest('attendance_date')
            ->latest('id')
            ->first();

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
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $groupIds = $teacher->groups()->pluck('groups.id');
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
            'students.*.status' => ['required', Rule::in($this->statuses)],
            'students.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $studentIds = Student::where('branch_id', $branchId)
            ->where('group_id', $data['group_id'])
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($data, $teacher, $branchId, $studentIds): void {
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

        return redirect()->route('teachers.attendance', ['group_id' => $data['group_id']])->with('status', 'Presensi berhasil disimpan.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher && $attendance->teacher_id === $teacher->id, 403);

        $attendance->delete();

        return redirect()->route('teachers.attendance')->with('status', 'Presensi berhasil dihapus.');
    }
}
