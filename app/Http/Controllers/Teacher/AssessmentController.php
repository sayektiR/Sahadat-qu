<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\LessonAssessment;
use App\Models\MemorizationAssessment;
use App\Models\Period;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Surah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        $branchId = Auth::user()->branch_id;

        return view('teachers.assessments.index', [
            'students' => Student::where('branch_id', $branchId)->orderBy('name')->get(),
            'groups' => Group::where('branch_id', $branchId)->orderBy('name')->get(),
            'subjects' => Subject::where('branch_id', $branchId)->orderBy('name')->get(),
            'surahs' => Surah::orderBy('number')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;
        $teacher = Auth::user()->teacher;

        abort_unless($teacher, 403);

        $base = $request->validate([
            'assessment_type' => ['required', Rule::in(['materi', 'hafalan'])],
            'student_id' => ['required', Rule::exists('students', 'id')->where('branch_id', $branchId)],
            'group_id' => ['required', Rule::exists('groups', 'id')->where('branch_id', $branchId)],
            'assessment_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $period = Period::where('branch_id', $branchId)->where('is_active', true)->firstOrFail();

        DB::transaction(function () use ($request, $base, $branchId, $teacher, $period): void {
            $score = null;

            if ($base['assessment_type'] === 'materi') {
                $detail = $request->validate([
                    'subject_id' => ['required', Rule::exists('subjects', 'id')->where('branch_id', $branchId)],
                    'score' => ['required', 'numeric', 'min:0', 'max:100'],
                ]);
                $score = (float) $detail['score'];
            } else {
                $detail = $request->validate([
                    'memorization_type' => ['required', 'string', 'max:255'],
                    'surah_id' => ['required', Rule::exists('surahs', 'id')],
                    'from_ayah' => ['required', 'integer', 'min:1'],
                    'to_ayah' => ['required', 'integer', 'gte:from_ayah'],
                    'movement_score' => ['required', 'numeric', 'min:0', 'max:100'],
                    'fluency_score' => ['required', 'numeric', 'min:0', 'max:100'],
                    'tajwid_score' => ['required', 'numeric', 'min:0', 'max:100'],
                    'makhraj_score' => ['required', 'numeric', 'min:0', 'max:100'],
                ]);

                $surah = Surah::findOrFail($detail['surah_id']);

                if ($detail['to_ayah'] > $surah->ayah_count) {
                    throw ValidationException::withMessages([
                        'to_ayah' => 'Ayat akhir tidak boleh melebihi jumlah ayat surah yang dipilih.',
                    ]);
                }

                $detail['surah'] = $surah->name;

                $score = (
                    (float) $detail['movement_score'] +
                    (float) $detail['fluency_score'] +
                    (float) $detail['tajwid_score'] +
                    (float) $detail['makhraj_score']
                ) / 4;
            }

            $assessment = Assessment::create([
                'branch_id' => $branchId,
                'group_id' => $base['group_id'],
                'student_id' => $base['student_id'],
                'teacher_id' => $teacher->id,
                'period_id' => $period->id,
                'assessment_type' => $base['assessment_type'],
                'assessment_date' => $base['assessment_date'],
                'final_score' => $score,
                'predicate' => Assessment::predicateFor($score),
                'note' => $base['note'] ?? null,
            ]);

            if ($base['assessment_type'] === 'materi') {
                LessonAssessment::create([
                    'assessment_id' => $assessment->id,
                    'subject_id' => $detail['subject_id'],
                    'score' => $score,
                ]);
            } else {
                MemorizationAssessment::create([
                    'assessment_id' => $assessment->id,
                    ...$detail,
                    'total_score' => $score,
                    'result_status' => Assessment::predicateFor($score),
                ]);
            }
        });

        return back()->with('status', 'Penilaian berhasil disimpan.');
    }
}
