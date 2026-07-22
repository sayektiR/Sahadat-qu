<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\Period;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentScoring;
use App\Models\AssessmentAttributeValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        $branchId = Auth::user()->branch_id;
        $teacher = Auth::user()->teacher;

        $assessmentTemplates = AssessmentTemplate::with([
            'attributes',
            'aspects',
        ])
        ->where('branch_id', $branchId)
        ->orderBy('name')
        ->get();

        $query = Assessment::with([
            'student',
            'group',
            'teacher',
            'template',
            'scorings.aspect',
            'attributeValues.attribute'
        ])
        ->where('branch_id', $branchId)
        ->where('teacher_id', $teacher->id);

        if (request('assessment_date')) {
            $query->whereDate(
                'assessment_date',
                request('assessment_date')
            );
        }

        $assessments = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

            // dd($assessmentTemplates->toArray());
            return view('teachers.assessments.index', [
            'assessmentTemplates' => $assessmentTemplates,
            'groups' => collect([$teacher->group]),
            'assessments' => $assessments,
            'teacher' => $teacher,
        ]);

    }

    public function store(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;
        $teacher = Auth::user()->teacher;

        abort_unless(
            $request->group_id == $teacher->group_id,
            403
        );

        $groupRule = Rule::exists('groups', 'id')->where('branch_id', $branchId);

        $base = $request->validate([
            'assessment_template_id' => [
                'required',
                Rule::exists('assessment_templates', 'id')->where('branch_id', $branchId),
            ],
            'group_id' => ['required', $groupRule],
            'assessment_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $template = AssessmentTemplate::with('aspects')
            ->where('branch_id', $branchId)
            ->findOrFail($base['assessment_template_id']);

        $period = Period::where('branch_id', $branchId)->where('is_active', true)->firstOrFail();

        $request->validate([ 
            'scores' => ['required', 'array'], 
            'scores.*' => ['array'],
            'scores.*.*' => ['required', 'numeric', 'between:0,100'],
        ]);
        
        $studentIds = $teacher->group
            ->students()
            ->pluck('id')
            ->toArray();
            
        DB::transaction(function () use ($request, $base, $template, $branchId, $teacher, $period, $studentIds,): void {
            
            foreach ($request->scores ?? [] as $studentId => $scores) {

                abort_unless(
                    in_array($studentId, $studentIds),
                    403
                );

                $aspects = $template->aspects;
                $totalWeight = $aspects->sum('weight');
                $total = 0;

                foreach ($aspects as $aspect) {
                    $nilai = $scores[$aspect->id] ?? 0;
                    $total += $nilai * $aspect->weight;
                }

                $average = $totalWeight > 0
                    ? round($total / $totalWeight, 2)
                    : 0;

                $assessment = Assessment::where('student_id', $studentId)
                    ->where('assessment_template_id', $base['assessment_template_id'])
                    ->whereDate('assessment_date', $base['assessment_date'])
                    ->where('period_id', $period->id)
                    ->first();

                if ($assessment) {
                    $assessment->update([
                        'branch_id' => $branchId,
                        'group_id' => $base['group_id'],
                        'teacher_id' => $teacher->id,
                        'final_score' => $average,
                        'predicate' => $request->input("predicates.$studentId")
                            ?? Assessment::predicateFor($average),
                        'note' => $base['note'] ?? null,
                    ]);
                } else {
                    $assessment = Assessment::create([
                        'student_id' => $studentId,
                        'assessment_template_id' => $base['assessment_template_id'],
                        'assessment_date' => $base['assessment_date'],
                        'period_id' => $period->id,
                        'branch_id' => $branchId,
                        'group_id' => $base['group_id'],
                        'teacher_id' => $teacher->id,
                        'final_score' => $average,
                        'predicate' => $request->input("predicates.$studentId")
                            ?? Assessment::predicateFor($average),
                        'note' => $base['note'] ?? null,
                    ]);
                }

                $assessment->scorings()->delete();
                $assessment->attributeValues()->delete();

                // Simpan aspek
                $templateAspectIds = $template->aspects->pluck('id')->toArray();

                foreach ($scores as $aspectId => $value) {

                    abort_unless(
                        in_array($aspectId, $templateAspectIds),
                        403
                    );

                    AssessmentScoring::create([
                        'assessment_id' => $assessment->id,
                        'assessment_aspect_id' => $aspectId,
                        'value' => $value,
                    ]);
                }

                // Simpan atribut
                // Simpan atribut (berlaku untuk semua santri)
                $attributes = $request->input('attributes', []);
                $templateAttributeIds = $template->attributes()->pluck('id')->toArray();

                foreach ($attributes as $attributeId => $value) {

                    abort_unless(
                        in_array($attributeId, $templateAttributeIds),
                        403
                    );

                    AssessmentAttributeValue::create([
                        'assessment_id' => $assessment->id,
                        'assessment_attribute_id' => $attributeId,
                        'value' => $value,
                    ]);
                }
            }
        });

        return back()->with('status', 'Penilaian berhasil disimpan.');
    }

    public function students(Group $group)
    {
        $teacher = Auth::user()->teacher;

        abort_unless(
            $group->id == $teacher->group_id,
            403
        );

        return response()->json(
            $group->students()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }

    public function destroy(Assessment $assessment)
    {
        $teacher = Auth::user()->teacher;

        abort_unless(
            $assessment->teacher_id == $teacher->id,
            403
        );

        DB::transaction(function () use ($assessment) {

            $assessment->scorings()->delete();

            $assessment->attributeValues()->delete();

            $assessment->delete();
        });

        return back()->with(
            'success',
            'Data penilaian berhasil dihapus.'
        );
    }

    
}
