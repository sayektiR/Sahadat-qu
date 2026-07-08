<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SubjectManagementController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;

        $subjects = Subject::where('branch_id', $branchId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSubject($request);

        Subject::create([
            'branch_id' => Auth::user()->branch_id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return back()->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $this->ensureSubjectBranch($subject);

        $data = $this->validateSubject($request, $subject);

        $subject->update($data);

        return back()->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $this->ensureSubjectBranch($subject);

        if ($subject->scheduleDetails()->exists()) {
            return back()->withErrors([
                'subject' => 'Mata pelajaran masih digunakan.'
            ]);
        }

        $subject->delete();

        return back()->with('status', 'Mata pelajaran berhasil dihapus.');
    }

    private function validateSubject(Request $request, ?Subject $subject = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects', 'name')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->ignore($subject?->id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);
    }

    private function ensureSubjectBranch(Subject $subject): void
    {
        abort_unless(
            $subject->branch_id === Auth::user()->branch_id,
            403
        );
    }
}