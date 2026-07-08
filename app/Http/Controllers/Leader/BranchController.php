<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::withCount([
                'students',
                'teachers',
                'users' => fn ($q) => $q->where('role', 'admin'),
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('head_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('leader.branches.index', compact('branches'));
    }

    public function show(Branch $branch): View
    {
        $branch->load([
            'groups.students',
            'students',
            'teachers',
            'periods',
            'users' => fn ($q) => $q->where('role', 'admin'),
        ]);

        return view('leader.branches.show', compact('branch'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:branches,name'],
            'address' => ['required', 'string'],
            'phone'   => ['required', 'string', 'max:20'],
        ]);

        Branch::create($validated);

        return back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:branches,name,' . $branch->id],
            'address' => ['required', 'string'],
            'phone'   => ['required', 'string', 'max:20'],
        ]);

        $branch->update($validated);

        return back()->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        if (
            $branch->students()->exists() ||
            $branch->teachers()->exists() ||
            $branch->users()->exists() ||
            $branch->groups()->exists() ||
            $branch->periods()->exists()
        ) {
            return back()->withErrors([
                'delete' => 'Cabang tidak dapat dihapus karena masih memiliki data yang terkait.',
            ]);
        }

        $branch->delete();

        return back()->with('success', 'Cabang berhasil dihapus.');
    }
}