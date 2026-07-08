<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Period;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $groups = collect();

        if ($teacher->group) {
            $groups = collect([$teacher->group]);
        }

        $groupIds = $groups->pluck('id');
        $activePeriod = Period::where('branch_id', Auth::user()->branch_id)->where('is_active', true)->first();

        $students = Student::with(['group', 'guardian'])
            ->withCount([
                'assessments as assessment_count' => fn ($query) => $query->where('teacher_id', $teacher->id),
                'attendanceDetails as attendance_count' => fn ($query) => $query->whereHas('attendance', fn ($attendance) => $attendance->where('teacher_id', $teacher->id)),
            ])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('group_id', $teacher->group_id)
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhereHas('guardian', fn ($guardian) => $guardian->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $studentIds = Student::where('branch_id', Auth::user()->branch_id)
            ->whereIn('group_id', $groupIds)
            ->pluck('id');

        return view('teachers.students.index', [
            'activePeriod' => $activePeriod,
            'averageScore' => round(
                Assessment::where('teacher_id', $teacher->id)
                ->whereIn('student_id', $studentIds)
                ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
                ->avg('final_score') ?? 0, 2,
            ),
            'groups' => $groups,
            'students' => $students,
            'teacher' => $teacher,
            'totalAssessments' => Assessment::where('teacher_id', $teacher->id)
                ->whereIn('student_id', $studentIds)
                ->when($activePeriod, fn ($query) => $query->where('period_id', $activePeriod->id))
                ->count(),
            'totalStudents' => $studentIds->count(),
        ]);
    }
}
