<?php

namespace Database\Seeders;


use App\Models\Branch;
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

                Report::updateOrCreate([
                    'branch_id' => $branch->id,
                    'student_id' => $student->id,
                    'period_id' => $period->id,
                ], [
                    'homeroom_teacher_id' => $teacher->id,
                    'final_note' => '-',
                    'report_date' => '2026-12-31',
                    'signed_by' => 'Kepala Cabang Sahadat-Qu',
                ]);
            });
    }
}
