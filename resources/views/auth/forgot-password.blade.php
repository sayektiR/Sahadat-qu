<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Sahadat-Qu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-950">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-slate-900">
                    Lupa Password?
                </h1>

    <p class="mt-2 text-sm text-slate-500">
        Masukkan email akun Anda untuk menerima link reset password.
    </p>
</div>

@if (session('status'))
    <div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<form
    method="POST"
    action="{{ route('password.email') }}"
    class="space-y-5">

    @csrf

    <div>
        <label
            for="email"
            class="block text-sm font-medium text-slate-700">
            Email
        </label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            required
            autofocus
            placeholder="Masukkan email"
            class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[#01C8B6] focus:ring-2 focus:ring-[#0B8C79]">

        @error('email')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <button
        type="submit"
        class="w-full cursor-pointer rounded-md bg-[#01C8B6] px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#0B8C79]">
        Kirim Link Reset Password
    </button>

    <div class="text-center">
        <a
            href="{{ route('login') }}"
            class="text-sm font-medium text-[#0B8C79] hover:text-[#086b5d]">
            Kembali ke Login
        </a>
    </div>

</form>
</html>