<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $guardian = Auth::user()->guardian;

        abort_unless($guardian, 403);

        $students = $guardian->students()->with('group')->get();

        return view('guardians.profile.index', [
            'guardian' => $guardian,
            'students' => $students,
        ]);
    }
}