<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::orderBy('name')->get();

        $query = Teacher::with(['user', 'branch']);

        $query
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')));

        $teachers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('leader.teachers.index', [
            'teachers' => $teachers,
            'branches' => $branches,
        ]);
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['user', 'branch']);

        return view('leader.teachers.show', [
            'teacher' => $teacher,
        ]);
    }
}
