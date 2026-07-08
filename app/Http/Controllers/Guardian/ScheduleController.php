<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $guardian = Auth::user()->guardian;
        $branchId = Auth::user()->branch_id;

        abort_unless($guardian, 403);

        $groupIds = $guardian->students->pluck('group_id')->filter()->unique();

        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;

        $schedules = Schedule::with(['group', 'period', 'details.subject'])
            ->where('branch_id', $branchId)
            ->where(function ($query) use ($groupIds) {
                $query->where('all_groups', true)
                    ->orWhereIn('group_id', $groupIds);
            })
            ->when($selectedPeriodId, fn ($query) => $query->where('period_id', $selectedPeriodId))
            ->orderBy('start_date')
            ->get();

        return view('guardians.schedules.index', [
            'activePeriod' => $activePeriod,
            'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'periods' => $periods,
            'schedules' => $schedules,
            'selectedPeriodId' => $selectedPeriodId,
        ]);
    }
}