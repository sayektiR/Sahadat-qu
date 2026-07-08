<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttribute;
use App\Models\AssessmentAspect;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\Period;
use App\Models\AssessmentTemplate;
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
            'student',
            'group',
            'teacher',
            'template',
            'scorings.aspect',
            'attributeValues.attribute'
        ])
            ->where('branch_id', $branchId)
            ->when($request->filled('assessment_template_id'), fn ($query) => $query->where('assessment_template_id', $request->integer('assessment_template_id')))
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
            'periods' => $periods,
            'totalCount' => (clone $baseQuery)->count(),
        ]);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AssessmentTemplate::create([
            'branch_id' => Auth::user()->branch_id,
            'name' => $request->name,
        ]);

        return redirect()->route('admin.settings.assessments.assessment-template')->with('status', 'Penilaian berhasil dibuat.');
    }

    public function update(Request $request, AssessmentTemplate $assessmentTemplate)
    {

        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $assessmentTemplate->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.settings.assessments.assessment-template')
            ->with('status', 'Penilaian berhasil diperbarui.');
    }

    public function destroy(AssessmentTemplate $assessmentTemplate)
    {
        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        $assessmentTemplate->delete();

        return redirect()
            ->route('admin.settings.assessments.assessment-template')
            ->with('status', 'Penilaian berhasil dihapus.');
    }

    //ATRIBUT
    public function attributes(AssessmentTemplate $assessmentTemplate): View
    {
        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        $attributes = AssessmentAttribute::where(
            'assessment_template_id',
            $assessmentTemplate->id
        )->get();

        return view(
            'admin.settings.assessments.attribute',
            compact('assessmentTemplate', 'attributes')
        );
    }

    public function storeAttribute(Request $request, AssessmentTemplate $assessmentTemplate)
    {

        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        $request->validate([
            'attribute_name' => 'required|string|max:255',
            'attribute_type' => 'required|in:text,number',
        ]);

        AssessmentAttribute::create([
            'assessment_template_id' => $assessmentTemplate->id,
            'attribute_name' => $request->attribute_name,
            'attribute_type' => $request->attribute_type,
        ]);

        return redirect()
            ->back()
            ->with('status', 'Atribut berhasil ditambahkan.');
    }

    public function updateAttribute(Request $request, AssessmentTemplate $assessmentTemplate, AssessmentAttribute $attribute)
    {

        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );
        
        abort_unless(
            $attribute->assessment_template_id == $assessmentTemplate->id,
            404
        );

        $request->validate([
            'attribute_name' => 'required|string|max:255',
            'attribute_type' => 'required|in:text,number',
        ]);

        $attribute->update([
            'assessment_template_id' => $assessmentTemplate->id,
            'attribute_name' => $request->attribute_name,
            'attribute_type' => $request->attribute_type,
        ]);

        return redirect()
            ->route('admin.settings.assessments.assessment-template.attributes', $assessmentTemplate)
            ->with('status', 'Atribut berhasil diperbarui.');
    }

    public function destroyAttribute(AssessmentTemplate $assessmentTemplate, AssessmentAttribute $attribute)
    {

        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        abort_unless(
            $attribute->assessment_template_id == $assessmentTemplate->id,
            404
        );

        $attribute->delete();

        return redirect()
            ->back()
            ->with('status', 'Atribut berhasil dihapus.');
    }
    
    public function aspects(AssessmentTemplate $assessmentTemplate): View
    {

        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        $aspects = AssessmentAspect::where(
            'assessment_template_id',
            $assessmentTemplate->id
        )->get();

        return view(
            'admin.settings.assessments.aspect',
            compact('assessmentTemplate', 'aspects')
        );
    }

    public function storeAspect(Request $request, AssessmentTemplate $assessmentTemplate)
    {
        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        $request->validate([
            'aspect_name' => 'required|string|max:255',
            'weight' => 'required|integer|min:0|max:100',
        ]);

        $totalWeight = AssessmentAspect::where(
            'assessment_template_id',
            $assessmentTemplate->id
        )->sum('weight');

        if (($totalWeight + $request->weight) > 100) {
            return back()
                ->withErrors([
                    'weight' => 'Total bobot tidak boleh melebihi 100%.'
                ])
                ->withInput();
        }

        $assessmentTemplate->aspects()->create([
            'aspect_name' => $request->aspect_name,
            'weight' => $request->weight,
        ]);

        return back()->with(
            'status',
            'Aspek penilaian berhasil ditambahkan.'
        );
    }

    public function updateAspect(Request $request, AssessmentTemplate $assessmentTemplate, AssessmentAspect $aspect)
    {
        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        abort_unless(
            $aspect->assessment_template_id == $assessmentTemplate->id,
            404
        );

        $totalWeight = AssessmentAspect::where(
            'assessment_template_id',
            $assessmentTemplate->id
        )
        ->where('id', '!=', $aspect->id)
        ->sum('weight');

        if (($totalWeight + $request->weight) > 100) {
            return back()
                ->withErrors([
                    'weight' => 'Total bobot tidak boleh melebihi 100%.'
                ])
                ->withInput();
        }

        $request->validate([
            'aspect_name' => 'required|string|max:255',
            'weight' => 'required|integer|min:0|max:100',
        ]);

        $aspect->update([
            'assessment_template_id' => $assessmentTemplate->id,
            'aspect_name' => $request->aspect_name,
            'weight' => $request->weight,
        ]);

        return redirect()
            ->route('admin.settings.assessments.assessment-template.aspects', $assessmentTemplate)
            ->with('status', 'Aspek berhasil diperbarui.');
    }

    public function destroyAspect(AssessmentTemplate $assessmentTemplate, AssessmentAspect $aspect)
    {
        abort_unless(
            $assessmentTemplate->branch_id == Auth::user()->branch_id,
            403
        );

        abort_unless(
            $aspect->assessment_template_id == $assessmentTemplate->id,
            404
        );

        $aspect->delete();

        return redirect()
            ->back()
            ->with('status', 'Aspek berhasil dihapus.');
    }
}
