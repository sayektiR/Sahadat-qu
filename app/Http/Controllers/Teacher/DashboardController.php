<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $teacher = Auth::user()->teacher;
        $branchId = Auth::user()->branch_id;

        abort_unless($teacher, 403);

        $groupIds = $teacher->groups->pluck('id');

        return view('teachers.dashboard', [
            'stats' => [
                'My Groups' => $groupIds->count(),
                'Students' => Student::where('branch_id', $branchId)->whereIn('group_id', $groupIds)->count(),
                'Schedules' => Schedule::where('branch_id', $branchId)->whereIn('group_id', $groupIds)->count(),
                'Assessments' => Assessment::where('teacher_id', $teacher->id)->count(),
            ],
            'teacher' => $teacher,
        ]);
    }
}