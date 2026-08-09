<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sahadat-Qu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-950">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mb-8 text-center">
                <img src="{{ asset('logo-sahadat-qu.jpeg') }}" alt="Sahadat-Qu" class="mx-auto mb-4 h-16 w-16 rounded-full object-cover">
                <h1 class="text-2xl font-semibold text-slate-950">Sahadat-Qu</h1>
                <p class="mt-2 text-sm text-slate-600">Sistem Akademik Rumah Tahfidz Sahadat-Qu</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan email" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[#01C8B6] focus:ring-2 focus:ring-[#0B8C79]">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <div class="mt-2" style="position: relative;">
                        <input id="password" name="password" type="password" required placeholder="Masukkan password" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[#01C8B6] focus:ring-2 focus:ring-[#0B8C79]" style="padding-right: 44px;">
                        <button id="toggle-password" type="button" class="cursor-pointer text-slate-500 hover:text-blue-950" style="position: absolute; top: 0; right: 0; display: flex; height: 100%; width: 44px; align-items: center; justify-content: center;" aria-label="Tampilkan password">
                            <svg id="eye-open" width="18" height="18" style="width: 18px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eye-closed" class="hidden" width="18" height="18" style="width: 18px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 3l18 18"/>
                                <path d="M10.6 10.6A3 3 0 0 0 13.4 13.4"/>
                                <path d="M9.9 4.4A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a16.3 16.3 0 0 1-3.1 4.4"/>
                                <path d="M6.1 6.1C3.4 8 2 12 2 12s3.5 8 10 8a10.8 10.8 0 0 0 5.9-1.8"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-[#01C8B6] focus:ring-[#01C8B6]">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#0B8C79] hover:text-[#086b5d]">
                        Lupa Password?
                    </a>
                </div>
                <button type="submit" class="w-full cursor-pointer rounded-md bg-[#01C8B6] px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#0B8C79]">Login</button>
            </form>
        </section>
    </main>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        togglePassword.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            togglePassword.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            eyeOpen.classList.toggle('hidden', isHidden);
            eyeClosed.classList.toggle('hidden', !isHidden);
        });
    </script>
</body>
</html>
