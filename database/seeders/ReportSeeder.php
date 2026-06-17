<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Branch;
use App\Models\LessonAssessment;
use App\Models\MemorizationAssessment;
use App\Models\Period;
use App\Models\Report;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Surah;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'Sahadat-Qu Bojonegoro Branch')->firstOrFail();
        $period = Period::where('branch_id', $branch->id)->where('name', '2026/2027 Ganjil')->firstOrFail();
        $subjects = Subject::where('branch_id', $branch->id)->orderBy('id')->take(3)->get();
        $teachers = Teacher::where('branch_id', $branch->id)->orderBy('id')->get();
        $alMulk = Surah::where('name', 'Al-Mulk')->firstOrFail();

        Student::with(['group.teachers'])
            ->where('branch_id', $branch->id)
            ->orderBy('id')
            ->get()
            ->each(function (Student $student, int $index) use ($branch, $period, $subjects, $teachers, $alMulk): void {
                $teacher = $student->group?->teachers?->first() ?: $teachers[$index % $teachers->count()];

                if (! Assessment::where('student_id', $student->id)->where('period_id', $period->id)->exists()) {
                    $subjects->each(function (Subject $subject, int $subjectIndex) use ($branch, $period, $student, $teacher, $index): void {
                        $score = 78 + (($index + $subjectIndex) % 18);

                        $assessment = Assessment::create([
                            'branch_id' => $branch->id,
                            'group_id' => $student->group_id,
                            'student_id' => $student->id,
                            'teacher_id' => $teacher->id,
                            'period_id' => $period->id,
                            'assessment_type' => 'materi',
                            'assessment_date' => '2026-08-' . str_pad((string) (10 + $subjectIndex), 2, '0', STR_PAD_LEFT),
                            'final_score' => $score,
                            'predicate' => Assessment::predicateFor($score),
                            'note' => 'Perkembangan materi baik dan perlu dijaga konsistensinya.',
                        ]);

                        LessonAssessment::create([
                            'assessment_id' => $assessment->id,
                            'subject_id' => $subject->id,
                            'score' => $score,
                        ]);
                    });

                    $memorizationScore = 80 + ($index % 15);
                    $memorization = Assessment::create([
                        'branch_id' => $branch->id,
                        'group_id' => $student->group_id,
                        'student_id' => $student->id,
                        'teacher_id' => $teacher->id,
                        'period_id' => $period->id,
                        'assessment_type' => 'hafalan',
                        'assessment_date' => '2026-08-20',
                        'final_score' => $memorizationScore,
                        'predicate' => Assessment::predicateFor($memorizationScore),
                        'note' => 'Hafalan bertambah dengan bacaan yang semakin lancar.',
                    ]);

                    MemorizationAssessment::create([
                        'assessment_id' => $memorization->id,
                        'memorization_type' => 'Setoran Hafalan',
                        'surah_id' => $alMulk->id,
                        'surah' => $alMulk->name,
                        'from_ayah' => 1,
                        'to_ayah' => 10 + ($index % 5),
                        'movement_score' => $memorizationScore,
                        'fluency_score' => $memorizationScore,
                        'tajwid_score' => max(0, $memorizationScore - 2),
                        'makhraj_score' => min(100, $memorizationScore + 1),
                        'total_score' => $memorizationScore,
                        'result_status' => Assessment::predicateFor($memorizationScore),
                        'examiner_1' => $teacher->name,
                    ]);
                }

                Report::updateOrCreate([
                    'branch_id' => $branch->id,
                    'student_id' => $student->id,
                    'period_id' => $period->id,
                ], [
                    'homeroom_teacher_id' => $teacher->id,
                    'final_note' => 'Ananda menunjukkan perkembangan yang baik. Mohon tetap dibimbing untuk menjaga murojaah dan kedisiplinan belajar di rumah.',
                    'report_date' => '2026-12-31',
                    'signed_by' => 'Kepala Cabang Sahadat-Qu',
                ]);
            });
    }
}
