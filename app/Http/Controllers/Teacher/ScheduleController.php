<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    private array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index(Request $request): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        abort_unless(
            !$request->filled('group_id') ||
            $request->integer('group_id') === $teacher->group_id,
            403
        );

        $groupId = $teacher->group_id;
        $groups = collect([$teacher->group]);
        $activePeriod = Period::where('branch_id', Auth::user()->branch_id)->where('is_active', true)->first();
        $periods = Period::where('branch_id', Auth::user()->branch_id)->orderByDesc('is_active')->orderByDesc('start_date')->get();
        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;
        $branchId = Auth::user()->branch_id;

        $schedulesQuery = Schedule::with(['group', 'period', 'details.subject'])
            ->where('branch_id', $branchId)
            ->where(function ($query) use ($groupId) {
                $query->where('group_id', $groupId)
                    ->orWhere('all_groups', true);
            })
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($selectedPeriodId, fn ($query) => $query->where('period_id', $selectedPeriodId))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->whereHas('group', fn ($group) => $group->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('period', fn ($period) => $period->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('details.subject', fn ($subject) => $subject->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('details', fn ($detail) => $detail->where('material_name', 'like', "%{$search}%"));
                });
            })
            ->latest('start_date');

        $schedules = $schedulesQuery->clone()->paginate(6)->withQueryString();

        $nextSchedule = $schedulesQuery->clone()
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->first();

        return view('teachers.schedules.index', [
            'activePeriod' => $activePeriod,
            'days' => $this->days,
            'groups' => $groups,
            'nextSchedule' => $nextSchedule?->load(['group', 'period', 'details.subject']),
            'periods' => $periods,
            'schedules' => $schedules,
            'teacher' => $teacher,
        ]);
    }
}
