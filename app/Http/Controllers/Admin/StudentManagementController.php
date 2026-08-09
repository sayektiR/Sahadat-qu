<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Imports\GuardianImport;
use App\Imports\StudentImport;
use App\Exports\GuardianTemplateExport;
use App\Exports\StudentTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
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
    
    public function createGuardian(): View
    {
        return view('admin.guardians.form', [
            'guardian' => null,
            'mode' => 'create',
        ]);
    }

    public function storeGuardian(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string'],
            'relation' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($data, $branchId): void {

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
            ]);
        });

        return redirect()->route('admin.guardians')->with('status', 'Data wali santri berhasil ditambahkan.');
    }


    public function editGuardian(Guardian $guardian): View
    {
        $this->ensureGuardianBranch($guardian);

        return view('admin.guardians.form', [
            'guardian' => $guardian,
            'mode' => 'edit',
        ]);
    }

    public function updateGuardian(Request $request, Guardian $guardian): RedirectResponse
    {
        $this->ensureGuardianBranch($guardian);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guardian->user_id)],
            'phone' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string'],
            'relation' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($guardian, $data): void {

            $guardian->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'relation' => $data['relation'] ?? null,
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

    public function searchGuardians(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        $guardians = Guardian::whereHas('user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get([
                'id',
                'name'
            ]);

        return response()->json($guardians);
    }

    //SANTRI
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


    public function createStudent(): View
    {
        $branchId = Auth::user()->branch_id;

        return view('admin.students.form', [
            'branch' => Auth::user()->branch,
            'groups' => Group::where('branch_id', $branchId)->orderBy('name')->get(),
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


    public function storeStudent(Request $request): RedirectResponse
    {
        $branchId = Auth::user()->branch_id;
        $data = $this->validateStudent($request, $branchId);
        $photo = $this->storePhoto($request, null, 'students');
        unset($data['photo']);
        $birthDate = \Carbon\Carbon::parse($request->birth_date);
        $data['nis'] = 'SQ-' . $birthDate->format('dm') . substr($request->nik, -3);

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
        
        if (
            $student->assessments()->exists() ||
            $student->reports()->exists() ||
            $student->attendanceDetails()->exists()
        ) {
            return back()->withErrors([
                'student' => 'Santri tidak dapat dihapus karena masih memiliki data yang berhubungan.'
            ]);
        }

        $student->delete();

        return redirect()->route('admin.students')->with('status', 'Data santri berhasil dihapus.');
    }


    private function validateStudent(Request $request, int $branchId, ?Student $student = null): array
    {
        return $request->validate([
            'group_id' => ['required', Rule::exists('groups', 'id')->where('branch_id', $branchId)],
            'guardian_id' => [
                'nullable',
                Rule::exists('guardians', 'id')
                    ->where(function ($query) use ($branchId) {
                        $query->whereIn('user_id', function ($sub) use ($branchId) {
                            $sub->select('id')
                                ->from('users')
                                ->where('branch_id', $branchId);
                        });
                    }),
            ],
            'nis' => ['nullable', 'string', 'max:10', Rule::unique('students', 'nis')->where('branch_id', $branchId)->ignore($student?->id),],
            'nik' => ['nullable', 'string', 'size:16',Rule::unique('students', 'nik')->ignore($student?->id),],
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ],
        [
            'group_id.required' => 'Grup santri harus dipilih.',
            'status.required' => 'Status santri harus dipilih.',
            'nik.size' => 'NIK harus terdiri dari 16 karakter.',
        ]);
    }

    public function guardianTemplate()
    {
        return Excel::download(
            new GuardianTemplateExport,
            'template-wali-santri.xlsx'
        );
    }

    public function importGuardians(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ]);

        try {

            Excel::import(
                new GuardianImport,
                $request->file('file')
            );

            return redirect()
                ->route('admin.guardians')
                ->with(
                    'status',
                    'Data wali santri berhasil diimport.'
                );

        } catch (\RuntimeException $e) {

            return redirect()
                ->route('admin.guardians')
                ->with(
                    'import_error',
                    $e->getMessage()
                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('admin.guardians')
                ->with(
                    'import_error',
                    'Terjadi kesalahan saat mengimport data. Silakan periksa file Excel dan coba lagi.'
                );
        }
    }

    public function studentTemplate()
    {
        return Excel::download(
            new StudentTemplateExport,
            'template-santri.xlsx'
        );
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

    public function importStudents(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ]);

        try {

            Excel::import(
                new StudentImport,
                $request->file('file')
            );

            return redirect()
                ->route('admin.students')
                ->with(
                    'status',
                    'Data santri berhasil diimport.'
                );

        } catch (\RuntimeException $e) {

            return redirect()
                ->route('admin.students')
                ->with(
                    'import_error',
                    $e->getMessage()
                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()
                ->route('admin.students')
                ->with(
                    'import_error',
                    'Terjadi kesalahan saat mengimport data. Silakan periksa file Excel dan coba lagi.'
                );
        }
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
