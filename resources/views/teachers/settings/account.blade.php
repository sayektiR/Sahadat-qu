<x-layouts.dashboard
    title="Pengaturan Akun"
    description="Kelola informasi akun Guru.">

    @if(session('success'))
        <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <nav class="flex items-center gap-2 text-sm">
        <span>Pengaturan</span>
        <span>/</span>
        <span class="font-semibold">Akun</span>
    </nav>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-900">
                    Informasi Akun
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Perbarui informasi akun Anda.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('teachers.settings.profile.update') }}"
                class="space-y-5 p-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-900">
                        Nama
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">
                        Nomor Telepon
                    </label>
                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div class="flex justify-end border-t border-slate-200 pt-5">
                    <button
                        type="submit"
                        class="cursor-pointer rounded-md bg-[#0B8C79] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]/90">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-900">
                    Ubah Password
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Pastikan password baru mudah diingat tetapi sulit ditebak.
                </p>
            </div>
            <form method="POST" action="{{ route('teachers.settings.password.update') }}" class="space-y-5 p-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-900">
                        Password Lama
                    </label>

                    <div class="relative mt-2">
                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            class="h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 pr-10 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">

                        <button
                            type="button"
                            onclick="togglePassword('current_password', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-blue-950">

                            <x-icon name="eye" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">
                        Password Baru
                    </label>

                    <div class="relative mt-2">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 pr-10 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">

                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-blue-950">

                            <x-icon name="eye" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">
                        Konfirmasi Password Baru
                    </label>

                    <div class="relative mt-2">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 pr-10 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">

                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-blue-950">

                            <x-icon name="eye" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                <div class="flex justify-end border-t border-slate-200 pt-5">
                    <button
                        type="submit"
                        class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function togglePassword(id, button) {
            const input = document.getElementById(id);
            const icon = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.add('text-blue-950');
            } else {
                input.type = 'password';
                icon.classList.remove('text-blue-950');
            }
        }
    </script>

</x-layouts.dashboard>