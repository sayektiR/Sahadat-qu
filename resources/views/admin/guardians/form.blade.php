@php
    $isEdit = $mode === 'edit';
    $pageTitle = $isEdit ? 'Edit Wali Santri' : 'Tambah Wali Santri';
    $action = $isEdit ? route('admin.guardians.update', $guardian) : route('admin.guardians.store');
@endphp

<x-layouts.dashboard :title="$pageTitle" description="Lengkapi data wali santri yang akan terhubung dengan akun login.">
    <form id="guardian-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="mb-6">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-950">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('admin.guardians') }}" class="hover:text-blue-950">Data Wali Santri</a>
                    <span>/</span>
                    <span class="font-medium text-slate-900">{{ $pageTitle }}</span>
                </nav>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="w-full space-y-5">

                <div class="space-y-5">
                    <h3 class="text-xl font-bold text-slate-950">Data Wali Santri</h3>
                    <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Lengkap</label>
                    <input name="name" value="{{ old('name', $guardian?->name) }}" required placeholder="Masukkan nama lengkap" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Email</label>
                    <input name="email" type="email" value="{{ old('email', $guardian?->user?->email) }}" required placeholder="Masukkan email" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">No. Telepon</label>
                        <input name="phone" value="{{ old('phone', $guardian?->phone) }}" placeholder="Masukkan nomor telepon" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div>
                        <label
                            for="relation"
                            class="mb-2 block text-sm font-medium text-slate-700">
                            Hubungan dengan Santri
                        </label>

                        <select id="relation" name="relation" class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10" required>

                            <option value="">Pilih Hubungan</option>

                            <option value="Ayah" @selected(old('relation', $guardian?->relation) === 'Ayah')>
                                Ayah
                            </option>

                            <option value="Ibu" @selected(old('relation', $guardian?->relation) === 'Ibu')>
                                Ibu
                            </option>

                            <option value="Wali Lainnya" @selected(old('relation', $guardian?->relation) === 'Wali Lainnya')>
                                Wali Lainnya
                            </option>

                        </select>

                        @error('relation')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Alamat</label>
                    <input name="address" value="{{ old('address', $guardian?->address) }}" placeholder="Masukkan alamat" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                @unless ($isEdit)
                    <p class="text-sm text-slate-500">Password default akun wali baru adalah <span class="font-semibold">password123</span>.</p>
                @endunless
            </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0B8C79]">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
            </div>
        </section>
    </form>


</x-layouts.dashboard>
