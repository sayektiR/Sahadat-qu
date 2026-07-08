<x-layouts.dashboard title="Pengaturan" description="Kelola profil cabang dan akun admin yang sedang digunakan.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="grid gap-6 xl:grid-cols-2">
        <form method="POST" action="{{ route('admin.settings.branch.update') }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-950">Profil Cabang</h2>
                <p class="mt-1 text-sm text-slate-500">Data ini dipakai untuk identitas laporan dan operasional cabang.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Cabang</label>
                    <input name="name" value="{{ old('name', $branch?->name) }}" required placeholder="Masukkan nama cabang" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Kepala Cabang</label>
                    <input name="head_name" value="{{ old('head_name', $branch?->head_name) }}" placeholder="Masukkan nama kepala cabang" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Telepon Cabang</label>
                    <input name="phone" value="{{ old('phone', $branch?->phone) }}" placeholder="Masukkan nomor telepon" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Alamat Cabang</label>
                    <textarea name="address" rows="4" placeholder="Masukkan alamat cabang" class="mt-2 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ old('address', $branch?->address) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0B8C79]">Simpan Cabang</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.settings.account.update') }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-950">Akun Admin</h2>
                <p class="mt-1 text-sm text-slate-500">Perbarui informasi login dan kontak admin cabang.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Admin</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama admin" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required placeholder="Masukkan email" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Telepon</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Masukkan nomor telepon" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Alamat</label>
                    <textarea name="address" rows="3" placeholder="Masukkan alamat admin" class="mt-2 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ old('address', $user->address) }}</textarea>
                </div>
                <div>
                    <label>Password Lama</label>
                    <input type="password" name="current_password" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    @error('current_password')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Password Baru</label>
                        <input name="password" type="password" placeholder="Kosongkan jika tidak diubah" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Konfirmasi Password</label>
                        <input name="password_confirmation" type="password" placeholder="Ulangi password baru" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0B8C79]">Simpan Akun</button>
            </div>
        </form>

        <div class="xl:col-span-2 mt-12 mb-2 border-t-2 border-slate-200 pt-6">
            <h2 class="text-2xl font-bold text-slate-950">Pengaturan Fitur</h2>
        </div>

        <!-- Pengaturan Fitur -->
        <form method="POST" action="#"
            class="xl:col-span-2 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-950">Pengaturan Penilaian</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Aktifkan atau nonaktifkan fitur yang tersedia pada sistem.
                </p>
            </div>

            <div class="grid gap-6">
                <div class="rounded-lg border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-slate-900">
                                Template Penilaian
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Kelola template penilaian yang digunakan pada proses evaluasi santri.
                            </p>
                        </div>

                        <a href="{{ route('admin.settings.assessments.assessment-template') }}"
                            class="rounded-md bg-blue-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                            Kelola
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layouts.dashboard>
