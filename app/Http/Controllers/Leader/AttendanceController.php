<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::orderBy('name')->get();
        $groups = Group::when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderBy('name')
            ->get();

        $query = Attendance::with(['branch', 'group.branch', 'teacher', 'period', 'details.student.group'])
            ->withCount('details')
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('branch', fn ($branch) => $branch->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('group.branch', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('group', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('details.student', fn ($s) => $s->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('attendance_date')
            ->latest('id');

        $attendances = $query->paginate(8)->withQueryString();

        return view('leader.attendance.index', [
            'attendances' => $attendances,
            'branches' => $branches,
            'groups' => $groups,
        ]);
    }

    public function show(Attendance $attendance): View
    {
        $attendance->load(['branch', 'group.branch', 'teacher', 'period', 'details.student.group']);

        $summary = $attendance->details->countBy('status');

        return view('leader.attendance.show', [
            'attendance' => $attendance,
            'summary' => $summary,
        ]);
    }
}
