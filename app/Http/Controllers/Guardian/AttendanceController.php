<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $guardian = Auth::user()->guardian;
        $branchId = Auth::user()->branch_id;

        abort_unless($guardian, 403);

        $studentIds = $guardian->students->pluck('id');
        $groupIds = $guardian->students->pluck('group_id')->filter();

        $groups = Group::whereIn('id', $groupIds)->orderBy('name')->get();
        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();
        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;
        abort_unless(
            !$request->filled('period_id') ||
            $periods->pluck('id')->contains($request->integer('period_id')),
            403
        );

        $attendances = Attendance::with([
            'group',
            'teacher',
            'details' => function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            },
        ])
        ->where('branch_id', $branchId)
        ->whereHas('details', function ($query) use ($studentIds) {
            $query->whereIn('student_id', $studentIds);
        })
        ->when($selectedPeriodId, function ($query) use ($selectedPeriodId) {
            $query->where('period_id', $selectedPeriodId);
        })
        ->latest('attendance_date')
        ->paginate(10)
        ->withQueryString();

        return view('guardians.attendance.index', [
            'activePeriod' => $activePeriod,
            'attendances' => $attendances,
            'groups' => $groups,
            'periods' => $periods,
            'selectedPeriodId' => $selectedPeriodId,
            'studentIds' => $studentIds,
        ]);
    }
}