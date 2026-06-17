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
        $periods = Period::where('branch_id', $branchId)->orderByDesc('is_active')->orderByDesc('start_date')->get();

        $selectedPeriodId = $request->integer('period_id') ?: $activePeriod?->id;
        $assessmentType = $request->input('assessment_type', 'materi') === 'hafalan' ? 'hafalan' : 'materi';

        $assessments = Assessment::with(['student', 'lessonAssessment.subject', 'memorizationAssessment'])
            ->where('branch_id', $branchId)
            ->whereIn('student_id', $studentIds)
            ->where('assessment_type', $assessmentType)
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($assessmentType === 'materi' && $request->filled('subject_id'), fn ($query) => $query->whereHas('lessonAssessment', fn ($q) => $q->where('subject_id', $request->integer('subject_id'))))
            ->when($selectedPeriodId, fn ($query) => $query->where('period_id', $selectedPeriodId))
            ->orderByDesc('assessment_date')
            ->paginate(10)
            ->withQueryString();

        return view('guardians.lesson-scores.index', [
            'activePeriod' => $activePeriod,
            'assessmentType' => $assessmentType,
            'assessments' => $assessments,
            'groups' => $groups,
            'periods' => $periods,
            'selectedPeriodId' => $selectedPeriodId,
            'subjects' => $subjects,
        ]);
    }
}
