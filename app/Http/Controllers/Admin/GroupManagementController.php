<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GroupManagementController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;

        $groups = Group::withCount(['students', 'teachers', 'schedules'])
            ->where('branch_id', $branchId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateGroup($request);

        Group::create([
            'branch_id' => Auth::user()->branch_id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('admin.groups')->with('status', 'Kelompok berhasil ditambahkan.');
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $this->ensureGroupBranch($group);
        $data = $this->validateGroup($request, $group);

        $group->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('admin.groups')->with('status', 'Kelompok berhasil diperbarui.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $this->ensureGroupBranch($group);

        if ($group->students()->exists() || $group->teachers()->exists() || $group->schedules()->exists()) {
            return back()->withErrors('Kelompok tidak bisa dihapus karena masih digunakan oleh santri, guru, atau jadwal.');
        }

        $group->delete();

        return redirect()->route('admin.groups')->with('status', 'Kelompok berhasil dihapus.');
    }

    private function validateGroup(Request $request, ?Group $group = null): array
    {
        $request->merge(['name' => trim($request->name),]);
        return $request->validate([ 
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('groups', 'name')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->ignore($group?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function ensureGroupBranch(Group $group): void
    {
        abort_unless($group->branch_id === Auth::user()->branch_id, 403);
    }
}
