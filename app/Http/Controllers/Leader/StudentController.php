<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::orderBy('name')->get();
        $groups = Group::when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderBy('name')
            ->get();

        $query = Student::with(['branch', 'group.branch', 'guardian']);

        $query
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhereHas('guardian', fn ($guardian) => $guardian->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')));

        $students = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('leader.students.index', [
            'students' => $students,
            'branches' => $branches,
            'groups' => $groups,
        ]);
    }

    public function show(Student $student): View
    {
        $student->load(['branch', 'group.branch', 'guardian']);

        return view('leader.students.show', [
            'student' => $student,
        ]);
    }
}
