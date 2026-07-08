<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeriodManagementController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;

        $periods = Period::withCount(['schedules', 'assessments', 'reports'])
            ->where('branch_id', $branchId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('academic_year', 'like', "%{$search}%")
                        ->orWhere('semester', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.periods.index', compact('periods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePeriod($request);
        $branchId = Auth::user()->branch_id;

        DB::transaction(function () use ($data, $branchId): void {
            if ($data['is_active'] ?? false) {
                Period::where('branch_id', $branchId)->update(['is_active' => false]);
            }

            Period::create([
                'branch_id' => $branchId,
                'name' => $data['name'],
                'academic_year' => $data['academic_year'],
                'semester' => $data['semester'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => $data['is_active'] ?? false,
            ]);
        });

        return redirect()->route('admin.periods')->with('status', 'Periode berhasil ditambahkan.');
    }

    public function update(Request $request, Period $period): RedirectResponse
    {
        $this->ensurePeriodBranch($period);
        $data = $this->validatePeriod($request, $period);
        $branchId = Auth::user()->branch_id;

        DB::transaction(function () use ($period, $data, $branchId): void {
            if ($data['is_active'] ?? false) {
                Period::where('branch_id', $branchId)->whereKeyNot($period->id)->update(['is_active' => false]);
            }

            $period->update([
                'name' => $data['name'],
                'academic_year' => $data['academic_year'],
                'semester' => $data['semester'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => $data['is_active'] ?? false,
            ]);
        });

        return redirect()->route('admin.periods')->with('status', 'Periode berhasil diperbarui.');
    }

    public function destroy(Period $period): RedirectResponse
    {
        $this->ensurePeriodBranch($period);

        if ($period->schedules()->exists() || $period->assessments()->exists() || $period->reports()->exists()) {
            return back()->withErrors('Periode tidak bisa dihapus karena masih digunakan oleh jadwal, penilaian, atau rapor.');
        }

        $period->delete();

        return redirect()->route('admin.periods')->with('status', 'Periode berhasil dihapus.');
    }

    private function validatePeriod(Request $request, ?Period $period = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('periods', 'name')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->ignore($period?->id),
            ],
            'academic_year' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($request, $period) {

            $exists = Period::where('branch_id', Auth::user()->branch_id)
                ->when($period, fn ($q) => $q->whereKeyNot($period->id))
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'start_date',
                    'Rentang tanggal bertabrakan dengan periode lain.'
                );
            }
        });

        return $validator->validate();
    }

    private function ensurePeriodBranch(Period $period): void
    {
        abort_unless($period->branch_id === Auth::user()->branch_id, 403);
    }
}
