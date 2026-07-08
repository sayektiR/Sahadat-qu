<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\Period;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LessonScoreController extends Controller
{
    public function index(Request $request): View
    {
        $guardian = Auth::user()->guardian;
        $branchId = Auth::user()->branch_id;

        abort_unless($guardian, 403);

        $studentIds = $guardian->students->pluck('id');
        $groupIds = $guardian->students->pluck('group_id')->filter();

        $groups = Group::whereIn('id', $groupIds)->orderBy('name')->get();
        $subjects = Subject::where('branch_id', $branchId)->orderBy('name')->get();
        $activePeriod = Period::where('branch_id', $branchId)->where('is_active', true)->first();
        $students = $guardian->students()->orderBy('name')->get();

        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;

        $assessments = Assessment::with([
            'student',
            'template',
            'scorings.aspect',
            'attributeValues.attribute',
        ])
            ->where('branch_id', $branchId)
            ->whereIn('student_id', $studentIds)
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->where('student_id', $request->integer('student_id'));
            })
            ->when($selectedPeriodId, fn($query) => $query->where('period_id', $selectedPeriodId))
            ->orderByDesc('assessment_date')
            ->paginate(10)
            ->withQueryString();

        return view('guardians.lesson-scores.index', [
            'activePeriod' => $activePeriod,
            'assessments' => $assessments,
            'students' => $students,
            'groups' => $groups,
            'selectedPeriodId' => $selectedPeriodId,
            'subjects' => $subjects,
        ]);
    }
}
