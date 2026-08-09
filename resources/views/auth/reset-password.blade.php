<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password - Sahadat-Qu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50">

    <main class="flex min-h-screen items-center justify-center px-4 py-10">

        <div class="w-full max-w-md">

            {{-- Card --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-8 shadow-lg">

                {{-- Header --}}
                <div class="mb-8 text-center">

                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#01C8B6]/10">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="26"
                            height="26"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#0B8C79"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <rect
                                width="18"
                                height="11"
                                x="3"
                                y="11"
                                rx="2"
                                ry="2"/>

                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>

                        </svg>
                    </div>

                    <h1 class="text-2xl font-bold text-slate-900">
                        Reset Password
                    </h1>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Buat password baru untuk akun Anda.
                    </p>

                </div>


                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <div class="flex gap-3">

                            <svg
                                class="mt-0.5 shrink-0 text-red-500"
                                xmlns="http://www.w3.org/2000/svg"
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" x2="12" y1="8" y2="12"/>
                                <line x1="12" x2="12.01" y1="16" y2="16"/>

                            </svg>

                            <p class="text-sm text-red-700">
                                {{ $errors->first() }}
                            </p>

                        </div>
                    </div>
                @endif


                {{-- Form --}}
                <form
                    method="POST"
                    action="{{ route('password.update') }}"
                    class="space-y-5">

                    @csrf

                    <input
                        type="hidden"
                        name="token"
                        value="{{ $token }}">


                    {{-- Email --}}
                    <div>

                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold text-slate-700">

                            Email

                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $email) }}"
                            readonly
                            class="h-11 w-full rounded-lg border border-slate-300 bg-slate-100 px-3 text-sm text-slate-600 outline-none">

                    </div>


                    {{-- Password Baru --}}
                    <div>

                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold text-slate-700">

                            Password Baru

                        </label>

                        <div class="relative">

                            <input id="password" name="password" type="password" required autofocus placeholder="Masukkan password baru" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-[#01C8B6] focus:ring-2 focus:ring-[#01C8B6]/20">
                            <button type="button" id="toggle-password" style="position: absolute; right: 0; top: 0; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: transparent;" class="z-10 text-slate-400 transition hover:text-[#0B8C79]" aria-label="Tampilkan password">
                                <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                <svg
                                    id="eye-closed"
                                    class="hidden"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="19"
                                    height="19"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M3 3l18 18"/>
                                    <path d="M10.6 10.6A3 3 0 0 0 13.4 13.4"/>
                                    <path d="M9.9 4.4A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a16.3 16.3 0 0 1-3.1 4.4"/>
                                    <path d="M6.1 6.1C3.4 8 2 12 2 12s3.5 8 10 8a10.8 10.8 0 0 0 5.9-1.8"/>

                                </svg>

                            </button>

                        </div>

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Konfirmasi Password --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="mb-2 block text-sm font-semibold text-slate-700">

                            Konfirmasi Password Baru

                        </label>

                        <div class="relative">

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                placeholder="Masukkan ulang password baru"
                                class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-[#01C8B6] focus:ring-2 focus:ring-[#01C8B6]/20">

                            <button
                                type="button"
                                id="toggle-confirm-password"
                                style="position: absolute; right: 0; top: 0; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: transparent;"
                                class="z-10 text-slate-400 transition hover:text-[#0B8C79]"
                                aria-label="Tampilkan password">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="19"
                                    height="19"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="3"/>

                                </svg>

                            </button>

                        </div>

                    </div>


                    {{-- Button --}}
                    <button
                        type="submit"
                        class="flex h-11 w-full cursor-pointer items-center justify-center rounded-lg bg-[#01C8B6] px-4 text-sm font-semibold text-white transition duration-200 hover:bg-[#0B8C79]">

                        Simpan Password Baru

                    </button>

                </form>


                {{-- Back Login --}}
                <div class="mt-6 border-t border-slate-100 pt-6 text-center">

                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-semibold text-[#0B8C79] transition hover:text-[#086b5d]">

                        ← Kembali ke Login

                    </a>

                </div>

            </section>

            <p class="mt-6 text-center text-xs text-slate-400">
                © {{ date('Y') }} Sahadat-Qu
            </p>

        </div>

    </main>


    <script>

        // Password baru
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        togglePassword.addEventListener('click', () => {

            const isHidden = passwordInput.type === 'password';

            passwordInput.type = isHidden ? 'text' : 'password';

            eyeOpen.classList.toggle('hidden', isHidden);
            eyeClosed.classList.toggle('hidden', !isHidden);

        });


        // Konfirmasi password
        const confirmInput = document.getElementById('password_confirmation');
        const toggleConfirm = document.getElementById('toggle-confirm-password');

        toggleConfirm.addEventListener('click', () => {

            const isHidden = confirmInput.type === 'password';

            confirmInput.type = isHidden ? 'text' : 'password';

        });

    </script>

</body>
</html>