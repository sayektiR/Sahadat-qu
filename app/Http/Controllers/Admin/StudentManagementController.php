<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentManagementController extends Controller
{
    public function createGuardian(): View
    {
        return view('admin.guardians.form', [
            'guardian' => null,
            'mode' => 'create',
        ]);
    }

    public function editGuardian(Guardian $guardian): View
    {
        $this->ensureGuardianBranch($guardian);

        return view('admin.guardians.form', [
            'guardian' => $guardian,
            'mode' => 'edit',
        ]);
    }

    public function createStudent(): View
    {
        $branchId = Auth::user()->branch_id;

        return view('admin.students.form', [
            'branch' => Auth::user()->branch,
            'groups' => Group::where('branch_id', $branchId)->orderBy('name')->get(),
            'guardians' => Guardian::whereHas('user', fn ($query) => $query->where('branch_id', $branchId))->orderBy('name')->get(),
            'mode' => 'create',
            'student' => null,
        ]);
    }

    public function editStudent(Student $student): View
    {
        $this->ensureStudentBranch($student);
        $branchId = Auth::user()->branch_id;

        return view('admin.students.form', [
            'branch' => Auth::user()->branch,
            'groups' => Group::where('branch_id', $branchId)->orderBy('name')->get(),
            'guardians' => Guardian::whereHas('user', fn ($query) => $query->where('branch_id', $branchId))->orderBy('name')->get(),
            'mode' => 'edit',
            'student' => $student,
        ]);
    }

    public function index(Request $request): View
    {
        $branchId = Auth::user()->branch_id;

        $groups = Group::where('branch_id', $branchId)->orderBy('name')->get();
        $guardiansForForm = Guardian::whereHas('user', fn ($query) => $query->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        $students = Student::with(['group', 'guardian'])
            ->where('branch_id', $branchId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhereHas('guardian', fn ($guardian) => $guardian->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('group_id'), fn ($query) => $query->where('group_id', $request->integer('group_id')))
            ->orderBy('name')
            ->paginate(9, ['*'], 'students_page')
            ->withQueryString();

        return view('admin.students.index', compact('groups', 'guardiansForForm', 'students'));
    }

    public function guardians(Request $request): View
    {
        $branchId = Auth::user()->branch_id;
        $groups = Group::where('branch_id', $branchId)->orderBy('name')->get();

        $guardians = Guardian::with(['students.group', 'user'])
            ->whereHas('user', fn ($query) => $query->where('branch_id', $branchId))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('students', fn ($student) => $student->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('group_id'), function ($query) use ($request) {
                $query->whereHas('students', fn ($student) => $student->where('group_id', $request->integer('group_id')));
            })
            ->orderBy('name')
            ->paginate(9, ['*'], 'guardians_page')
            ->withQueryString();

        return view('admin.guardians.index', compact('groups', 'guardians'));
    }

    public function storeGuardian(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'relation' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        DB::transaction(function () use ($data, $branchId): void {
            $photo = $this->storePhoto(request(), null, 'guardians');
            unset($data['photo']);

            $user = User::create([
                'branch_id' => $branchId,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'guardian',
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            Guardian::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'relation' => $data['relation'] ?? null,
                'photo' => $photo,
            ]);
        });

        return redirect()->route('admin.guardians')->with('status', 'Data wali santri berhasil ditambahkan.');
    }

    public function updateGuardian(Request $request, Guardian $guardian): RedirectResponse
    {
        $this->ensureGuardianBranch($guardian);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guardian->user_id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'relation' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        DB::transaction(function () use ($guardian, $data): void {
            $photo = $this->storePhoto(request(), $guardian->photo, 'guardians');
            unset($data['photo']);

            $guardian->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'relation' => $data['relation'] ?? null,
                'photo' => $photo,
            ]);

            $guardian->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
        });

        return redirect()->route('admin.guardians')->with('status', 'Data wali santri berhasil diperbarui.');
    }

    public function destroyGuardian(Guardian $guardian): RedirectResponse
    {
        $this->ensureGuardianBranch($guardian);

        if ($guardian->students()->exists()) {
            return back()->withErrors(['guardian' => 'Wali santri masih terhubung dengan santri. Pindahkan data santri terlebih dahulu.']);
        }

        $guardian->user()->delete();

        return redirect()->route('admin.guardians')->with('status', 'Data wali santri berhasil dihapus.');
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;
        $data = $this->validateStudent($request, $branchId);
        $photo = $this->storePhoto($request, null, 'students');
        unset($data['photo']);

        Student::create(['branch_id' => $branchId, ...$data, 'photo' => $photo]);

        return redirect()->route('admin.students')->with('status', 'Data santri berhasil ditambahkan.');
    }

    public function updateStudent(Request $request, Student $student): RedirectResponse
    {
        $this->ensureStudentBranch($student);
        $data = $this->validateStudent($request, Auth::user()->branch_id, $student);
        $photo = $this->storePhoto($request, $student->photo, 'students');
        unset($data['photo']);

        $student->update([...$data, 'photo' => $photo]);

        return redirect()->route('admin.students')->with('status', 'Data santri berhasil diperbarui.');
    }

    public function destroyStudent(Student $student): RedirectResponse
    {
        $this->ensureStudentBranch($student);
        $student->delete();

        return redirect()->route('admin.students')->with('status', 'Data santri berhasil dihapus.');
    }

    private function validateStudent(Request $request, int $branchId, ?Student $student = null): array
    {
        return $request->validate([
            'group_id' => ['required', Rule::exists('groups', 'id')->where('branch_id', $branchId)],
            'guardian_id' => ['nullable', Rule::exists('guardians', 'id')],
            'nis' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'nis')
                    ->where('branch_id', $branchId)
                    ->ignore($student?->id),
            ],
            'nik' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function storePhoto(Request $request, ?string $oldPath, string $directory): ?string
    {
        if (! $request->hasFile('photo')) {
            return $oldPath;
        }

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $request->file('photo')->store($directory, 'public');
    }

    private function ensureStudentBranch(Student $student): void
    {
        abort_unless($student->branch_id === Auth::user()->branch_id, 403);
    }

    private function ensureGuardianBranch(Guardian $guardian): void
    {
        abort_unless($guardian->user?->branch_id === Auth::user()->branch_id, 403);
    }
}
