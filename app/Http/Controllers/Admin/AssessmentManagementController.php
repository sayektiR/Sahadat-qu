<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentManagementController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;
        $groups = Group::where('branch_id', $branchId)->orderBy('name')->get();
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $baseQuery = Assessment::where('branch_id', $branchId);

        $assessments = Assessment::with([
            'group',
            'student.guardian',
            'teacher',
            'period',
            'lessonAssessment.subject',
            'memorizationAssessment',
        ])
            ->where('branch_id', $branchId)
            ->when($request->filled('assessment_type'), fn ($query) => $query->where('assessment_type', $request->string('assessment_type')))
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($request->filled('period_id'), fn ($query) => $query->where('period_id', $request->integer('period_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->whereHas('student', fn ($student) => $student->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('teacher', fn ($teacher) => $teacher->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('group', fn ($group) => $group->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('assessment_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.assessments.index', [
            'assessments' => $assessments,
            'averageScore' => (clone $baseQuery)->avg('final_score'),
            'groups' => $groups,
            'lessonCount' => (clone $baseQuery)->where('assessment_type', 'materi')->count(),
            'memorizationCount' => (clone $baseQuery)->where('assessment_type', 'hafalan')->count(),
            'periods' => $periods,
            'totalCount' => (clone $baseQuery)->count(),
        ]);
    }
}
