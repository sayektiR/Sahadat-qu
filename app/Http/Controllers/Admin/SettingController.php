<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'branch' => Auth::user()->branch,
            'user' => Auth::user(),
        ]);
    }

    public function updateBranch(Request $request): RedirectResponse
    {
        $branch = Auth::user()->branch;
        abort_unless($branch, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'head_name' => ['nullable', 'string', 'max:255'],
        ]);

        $branch->update($data);

        return redirect()->route('admin.settings')->with('status', 'Pengaturan cabang berhasil diperbarui.');
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => filled($data['password'] ?? null) ? Hash::make($data['password']) : $user->password,
        ]);

        return redirect()->route('admin.settings')->with('status', 'Pengaturan akun berhasil diperbarui.');
    }
}
