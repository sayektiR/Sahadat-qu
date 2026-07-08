<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::orderBy('name')->get();

        $admins = User::where('role', 'admin')
            ->with(['branch'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('leader.admins.index', [
            'admins' => $admins,
            'branches' => $branches,
        ]);
    }

    public function create(): View
    {
        $branches = Branch::orderBy('name')->get();

        return view('leader.admins.form', [
            'admin' => null,
            'branches' => $branches,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'branch_id' => $validated['branch_id'],
            'role' => 'admin',
        ]);

        return redirect()->route('leader.admins')
            ->with('status', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $admin): View
    {
        abort_unless($admin->role === 'admin', 404);

        $admin->load('branch');
        $branches = Branch::orderBy('name')->get();

        return view('leader.admins.form', [
            'admin' => $admin,
            'branches' => $branches,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->role === 'admin', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'branch_id' => $validated['branch_id'],
        ]);

        if (filled($validated['password'] ?? null)) {
            $admin->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('leader.admins')
            ->with('status', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        abort_unless($admin->role === 'admin', 404);

        $admin->delete();

        return redirect()->route('leader.admins')
            ->with('status', 'Admin berhasil dihapus.');
    }
}
