<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScheduleManagementController extends Controller
{
    private array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;
        $groups = Group::where('branch_id', $branchId)->orderBy('name')->get();

        $schedules = Schedule::with(['group', 'period', 'details.subject'])
            ->where('branch_id', $branchId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->whereHas('group', fn ($group) => $group->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('period', fn ($period) => $period->where('name', 'like', "%{$search}%"));
            })
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->latest('start_date')
            ->paginate(6)
            ->withQueryString();

        return view('admin.schedules.index', [
            'days' => $this->days,
            'groups' => $groups,
            'schedules' => $schedules,
        ]);
    }

    public function create(): View
    {
        return $this->formView(new Schedule(), 'create');
    }

    public function store(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;
        $data = $this->validateSchedule($request, $branchId);

        DB::transaction(function () use ($data, $request, $branchId): void {
            $schedule = Schedule::create([
                'branch_id'       => $branchId,
                'group_id'        => $request->boolean('all_groups') ? null : $data['group_id'],
                'all_groups'      => $request->boolean('all_groups'),
                'period_id'       => $data['period_id'],
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
                'start_time'      => $data['start_time'],
                'end_time'        => $data['end_time'],
                'total_meetings'  => $data['total_meetings'],
            ]);

            $this->syncDetails($schedule, $data['details'] ?? []);
        });

        return redirect()->route('admin.schedules')->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule): View
    {
        $this->ensureScheduleBranch($schedule);

        return $this->formView($schedule->load('details'), 'edit');
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $this->ensureScheduleBranch($schedule);
        $data = $this->validateSchedule($request, Auth::user()->branch_id, $schedule);

        DB::transaction(function () use ($schedule, $request, $data): void {
            $schedule->update([
                'group_id' => $request->boolean('all_groups')
                    ? null
                    : $data['group_id'],

                'all_groups' => $request->boolean('all_groups'),

                'period_id' => $data['period_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'total_meetings' => $data['total_meetings'],
            ]);

            $schedule->details()->delete();
            $this->syncDetails($schedule, $data['details'] ?? []);
        });

        return redirect()->route('admin.schedules')->with('status', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $this->ensureScheduleBranch($schedule);
        $schedule->delete();

        return redirect()->route('admin.schedules')->with('status', 'Jadwal berhasil dihapus.');
    }

    private function formView(Schedule $schedule, string $mode): View
    {
        $branchId = Auth::user()->branch_id;

        return view('admin.schedules.form', [
            'days' => $this->days,
            'detailsByDay' => $schedule->exists ? $schedule->details->groupBy('day') : collect(),
            'groups' => Group::where('branch_id', $branchId)->orderBy('name')->get(),
            'mode' => $mode,
            'periods' => Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get(),
            'schedule' => $schedule,
            'subjects' => Subject::where('branch_id', $branchId)->orderBy('name')->get(),
        ]);
    }

    private function validateSchedule(Request $request, int $branchId, ?Schedule $schedule = null): array
    {
        $data = $request->validate([
            'group_id' => ['nullable', Rule::exists('groups', 'id')->where('branch_id', $branchId), ],
            'period_id' => ['required', Rule::exists('periods', 'id')->where('branch_id', $branchId)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'total_meetings' => ['required', 'integer', 'min:1'],
            'details' => ['nullable', 'array'],
            'details.*.*.subject_id' => ['nullable', Rule::exists('subjects', 'id')->where('branch_id', $branchId)],
            'details.*.*.material_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (
            ! $request->boolean('all_groups') &&
            empty($data['group_id'])
        ) {
            throw ValidationException::withMessages([
                'group_id' => 'Silakan pilih kelompok atau centang "Berlaku untuk semua kelompok".',
            ]);
        }

        $period = Period::where('branch_id', $branchId)->findOrFail($data['period_id']);

        if (
            $data['start_date'] < $period->start_date->format('Y-m-d') ||
            $data['end_date'] > $period->end_date->format('Y-m-d')
        ) {
            throw ValidationException::withMessages([
                'start_date' => 'Rentang tanggal jadwal harus berada di dalam periode yang dipilih.',
            ]);
        }

        return $data;
    }

    private function syncDetails(Schedule $schedule, array $details): void
    {
        foreach ($this->days as $day) {
            $orderNumber = 1;

            foreach (($details[$day] ?? []) as $detail) {
                if (empty($detail['subject_id']) && empty($detail['material_name'])) {
                    continue;
                }

                $schedule->details()->create([
                    'day' => $day,
                    'subject_id' => $detail['subject_id'] ?: null,
                    'material_name' => $detail['material_name'] ?: null,
                    'order_number' => $orderNumber++,
                ]);
            }
        }
    }

    private function ensureScheduleBranch(Schedule $schedule): void
    {
        abort_unless($schedule->branch_id === Auth::user()->branch_id, 403);
    }
}
