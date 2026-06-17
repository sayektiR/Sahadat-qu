<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::withCount(['students', 'teachers', 'users' => fn ($q) => $q->where('role', 'admin')])
            ->orderBy('name')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('head_name', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('leader.branches.index', [
            'branches' => $branches,
        ]);
    }

    public function show(Branch $branch): View
    {
        $branch->load(['groups.students', 'students', 'teachers', 'periods', 'users' => fn ($q) => $q->where('role', 'admin')]);

        return view('leader.branches.show', [
            'branch' => $branch,
        ]);
    }
}
