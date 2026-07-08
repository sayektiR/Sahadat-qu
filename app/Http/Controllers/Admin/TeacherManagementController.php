<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherManagementController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;

        $groups = Group::where('branch_id', $branchId)
        ->orderBy('name')
        ->get();


        $teachers = Teacher::with(['user', 'group'])
            ->where('branch_id', $branchId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) =>
                            $user->where('email', 'like', "%{$search}%")
                        );
                });
        })
        ->when($request->filled('group_id'), function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            })
        ->orderBy('name')
        ->paginate(9)
        ->withQueryString();
            

       
        return view('admin.teachers.index', compact('teachers', 'groups'));
    }

    public function create(): View
    {
        return view('admin.teachers.form', [
            'groups' => Group::where('branch_id', Auth::user()->branch_id)->orderBy('name')->get(),
            'mode' => 'create',
            'teacher' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;
        $data = $this->validateTeacher($request);
        $photo = $this->storePhoto($request, null);
        unset($data['photo']);

        DB::transaction(function () use ($data, $branchId, $photo): void {
            $user = User::create([
                'branch_id' => $branchId,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'group_id' => $data['group_id'] ?? null,
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => $data['status'],
                'photo' => $photo,
            ]);
        });

        return redirect()->route('admin.teachers')->with('status', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher): View
    {
        $this->ensureTeacherBranch($teacher);

        return view('admin.teachers.form', [
            'groups' => Group::where('branch_id', Auth::user()->branch_id)->orderBy('name')->get(),
            'mode' => 'edit',
            'teacher' => $teacher->load(['user', 'group']),
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $this->ensureTeacherBranch($teacher);

        $data = $this->validateTeacher($request, $teacher);
        $photo = $this->storePhoto($request, $teacher->photo);
        unset($data['photo']);

        DB::transaction(function () use ($teacher, $data, $photo): void {

            // Update data guru
            $teacher->update([
                'group_id' => $data['group_id'] ?? null,
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => $data['status'],
                'photo' => $photo,
            ]);

            // Jika email diisi
            if (!empty($data['email'])) {

                if ($teacher->user) {

                    // Update akun lama
                    $teacher->user->update([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'] ?? null,
                        'address' => $data['address'] ?? null,
                    ]);

                } else {

                    // Buat akun baru
                    $user = User::create([
                        'branch_id' => $teacher->branch_id,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make('password123'),
                        'role' => 'teacher',
                        'phone' => $data['phone'] ?? null,
                        'address' => $data['address'] ?? null,
                    ]);

                    $teacher->update([
                        'user_id' => $user->id,
                    ]);
                }

            }
        });

        return redirect()->route('admin.teachers')
            ->with('status', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $this->ensureTeacherBranch($teacher);

        if ($teacher->user) {
            $teacher->user->delete();
        }

        $teacher->user()->delete();
        $teacher->delete();

        return redirect()->route('admin.teachers')->with('status', 'Data guru berhasil dihapus.');
    }

    private function validateTeacher(Request $request, ?Teacher $teacher = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($teacher?->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'],
            'group_id' => ['nullable', Rule::exists('groups', 'id')->where('branch_id', Auth::user()->branch_id),]

        ]);
    }

    private function storePhoto(Request $request, ?string $oldPath): ?string
    {
        if (! $request->hasFile('photo')) {
            return $oldPath;
        }

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $request->file('photo')->store('teachers', 'public');
    }

    private function ensureTeacherBranch(Teacher $teacher): void
    {
        abort_unless($teacher->branch_id === Auth::user()->branch_id, 403);
    }
}
