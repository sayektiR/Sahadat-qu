<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentTemplate;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::orderBy('name')->get();
        $groups = Group::when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderBy('name')
            ->get();
        $periods = Period::when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->get();

        $query = Assessment::with([
            'branch',
            'group.branch',
            'student.guardian',
            'teacher',
            'period',
            'template',
            'scorings.aspect',
            'attributeValues.attribute',
        ])
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when(
                $request->filled('assessment_template_id'),
                fn ($query) =>
                    $query->where(
                        'assessment_template_id',
                        $request->integer('assessment_template_id')
                    )
            )
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($request->filled('period_id'), fn ($query) => $query->where('period_id', $request->integer('period_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('branch', fn ($branch) => $branch->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('group', fn ($g) => $g->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('assessment_date')
            ->latest('id');

            $templates = AssessmentTemplate::when(
                $request->filled('branch_id'),
                fn ($query) => $query->where(
                    'branch_id',
                    $request->integer('branch_id')
                )
            )
            ->orderBy('name')
            ->get();

        $assessments = $query->paginate(10)->withQueryString();

        return view('leader.assessments.index', [
            'assessments' => $assessments,
            'branches' => $branches,
            'templates' => $templates,
            'groups' => $groups,
            'periods' => $periods,
        ]);
    }

    public function show(Assessment $assessment): View
    {
        $assessment->load([
            'branch',
            'group.branch',
            'student.guardian',
            'teacher',
            'period',
            'template',
            'scorings.aspect',
            'attributeValues.attribute',
        ]);

        return view('leader.assessments.show', [
            'assessment' => $assessment,
        ]);
    }
}
