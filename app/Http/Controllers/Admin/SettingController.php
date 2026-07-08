<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\User;
use App\Models\AssessmentTemplate;


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
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],

            'current_password' => [
                'nullable',
                'required_with:password',
                'current_password',
            ],

            'password' => [
                'nullable',
                'confirmed',
                'min:8',
            ],
        ]);

        if ($request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors([
                        'current_password' => 'Password lama tidak sesuai.'
                    ])
                    ->withInput();
            }
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => filled($data['password'] ?? null) ? Hash::make($data['password']) : $user->password,
        ]);

        return redirect()->route('admin.settings')->with('status', 'Pengaturan akun berhasil diperbarui.');
    }

    public function assessmentTemplates(): View
    {

        $assessmentTemplates = AssessmentTemplate::where(
            'branch_id',
            Auth::user()->branch_id
        )
        ->orderBy('name')
        ->get();

        return view(
            'admin.settings.assessments.assessment-template',
            compact('assessmentTemplates')
        );
    }

    public function attributes(Request $request, AssessmentTemplate $assessmentTemplate)
    {
        $search = $request->search;

        $attributes = $assessmentTemplate->attributes()
            ->when($search, function ($query) use ($search) {
                $query->where('attribute_name', 'like', "%{$search}%");
            })
            ->get();

        return view('admin.settings.assessments.attribute', compact(
            'assessmentTemplate',
            'attributes'
        ));
    }
}
