<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('leader.dashboard', [
            'stats' => [
                'branches' => Branch::count(),
                'admins' => User::where('role', 'admin')->count(),
                'students' => Student::count(),
                'teachers' => Teacher::count(),
                'assessments' => Assessment::count(),
                'attendances' => Attendance::count(),
            ],
            'recentBranches' => Branch::withCount(['students', 'teachers'])->orderByDesc('created_at')->take(5)->get(),
            'recentAssessments' => Assessment::with(['branch', 'student', 'teacher'])
                ->latest('assessment_date')
                ->take(5)
                ->get(),
        ]);
    }
}
