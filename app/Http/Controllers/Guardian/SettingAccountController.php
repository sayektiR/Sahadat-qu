<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingAccountController extends Controller
{
    public function index(): View
    {
        return view('guardians.settings.account', [
            'user' => Auth::user(),
        ]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        /** @var User $user */
        $user = Auth::user();


        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'=>[
                'required',
                'current_password',
            ],

            'password'=>[
                'required',
                'confirmed',
                Password::defaults(),
            ]
        ]);


        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success','Password berhasil diubah.');
    }
}