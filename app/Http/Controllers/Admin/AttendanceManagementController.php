<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceManagementController extends Controller
{
    private array $statuses = ['hadir', 'izin', 'sakit', 'alpha'];

    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;
        $groups = Group::where('branch_id', $branchId)->orderBy('name')->get();

        $attendances = Attendance::with(['group', 'teacher', 'period', 'details.student.group'])
            ->withCount('details')
            ->where('branch_id', $branchId)
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->whereHas('group', fn ($group) => $group->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('teacher', fn ($teacher) => $teacher->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('details.student', fn ($student) => $student->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        $latestDate = Attendance::where('branch_id', $branchId)->max('attendance_date');
        $latestAttendances = collect();

        if ($latestDate) {
            $latestAttendances = Attendance::with([
                'group',
                'teacher',
                'period',
                'details.student.group',
            ])
            ->where('branch_id', $branchId)
            ->whereDate('attendance_date', $latestDate)
            ->when(
                $request->filled('group_id'),
                fn ($query) => $query->where('group_id', $request->integer('group_id'))
            )
            ->get();
        }

        $latestRows = $latestAttendances->flatMap(function ($attendance) {
            return $attendance->details->map(fn ($detail) => [
                'attendance' => $attendance,
                'detail' => $detail,
            ]);
        })
            ->when($request->filled('search'), function ($rows) use ($request) {
                $search = strtolower($request->string('search'));

                return $rows->filter(function ($row) use ($search) {
                    return str_contains(strtolower($row['detail']->student?->name ?? ''), $search)
                        || str_contains(strtolower($row['detail']->student?->group?->name ?? ''), $search)
                        || str_contains(strtolower($row['attendance']->teacher?->name ?? ''), $search);
                });
            })
            ->values();

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'groups' => $groups,
            'latestDate' => $latestDate,
            'latestRows' => $latestRows,
            'statuses' => $this->statuses,
        ]);
    }

    
    public function edit(Attendance $attendance): View
    {
        $this->ensureAttendanceBranch($attendance);

        $attendance->load([
            'group',
            'teacher',
            'period',
            'details.student',
        ]);

        return view('admin.attendance.edit', compact('attendance'));
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->ensureAttendanceBranch($attendance);

        $data = $request->validate([
            'details' => ['required', 'array'],
            'details.*.status' => [
                'required',
                Rule::in($this->statuses),
            ],
            'details.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($attendance, $data) {

            foreach ($data['details'] as $detailId => $detail) {

                $attendance->details()
                    ->whereKey($detailId)
                    ->update([
                        'status' => $detail['status'],
                        'note' => $detail['note'] ?? null,
                    ]);
            }

        });

        return redirect()
            ->route('admin.attendance')
            ->with('status', 'Presensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $this->ensureAttendanceBranch($attendance);
        $attendance->delete();

        return redirect()->route('admin.attendance')->with('status', 'Presensi berhasil dihapus.');
    }

    private function ensureAttendanceBranch(Attendance $attendance): void
    {
        abort_unless($attendance->branch_id === Auth::user()->branch_id, 403);
    }
}
