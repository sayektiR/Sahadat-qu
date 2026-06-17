@php
    $isEdit = $mode === 'edit';
    $pageTitle = $isEdit ? 'Edit Santri' : 'Tambah Santri';
    $action = $isEdit ? route('admin.students.update', $student) : route('admin.students.store');
@endphp

<x-layouts.dashboard :title="$pageTitle" description="Lengkapi data identitas, kelompok, dan wali santri.">
    <form id="student-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="mb-6">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-950">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('admin.students') }}" class="hover:text-blue-950">Data Santri</a>
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

        <section class="w-fit max-w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="grid gap-8 lg:grid-cols-[170px_minmax(0,1fr)] xl:grid-cols-[190px_minmax(0,760px)]">
                <aside>
                    <div class="flex h-40 w-40 items-center justify-center overflow-hidden rounded-md bg-slate-300 text-slate-900">
                        <img id="photo-preview" src="{{ $student?->photo ? asset('storage/' . $student->photo) : '' }}" alt="Preview photo" class="{{ $student?->photo ? '' : 'hidden' }} h-full w-full object-cover">
                        <svg id="photo-placeholder" class="{{ $student?->photo ? 'hidden' : '' }}" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="3"/>
                            <path d="M5 21a7 7 0 0 1 14 0"/>
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    </div>
                    <input id="photo-input" name="photo" type="file" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('photo-input').click()" class="mt-3 h-8 w-40 cursor-pointer rounded-md border border-slate-500 bg-white text-xs font-medium text-slate-700 hover:border-blue-950 hover:text-blue-950">
                        Ubah Photo
                    </button>
                </aside>

                <div class="space-y-5">
                    <h3 class="text-xl font-bold text-slate-950">Data Santri</h3>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">NIS</label>
                        <input name="nis" value="{{ old('nis', $student?->nis) }}" placeholder="Masukkan NIS santri" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900">NIK</label>
                        <input name="nik" value="{{ old('nik', $student?->nik) }}" placeholder="Masukkan NIK" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Lengkap</label>
                    <input name="name" value="{{ old('name', $student?->name) }}" required placeholder="Masukkan nama lengkap" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Tempat</label>
                        <input name="birth_place" value="{{ old('birth_place', $student?->birth_place) }}" placeholder="Masukkan tempat lahir" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Tanggal Lahir</label>
                        <input name="birth_date" type="date" value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d')) }}" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Jenis Kelamin</label>
                    <select name="gender" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih jenis kelamin</option>
                        <option value="female" @selected(old('gender', $student?->gender) === 'female')>Perempuan</option>
                        <option value="male" @selected(old('gender', $student?->gender) === 'male')>Laki-laki</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Alamat</label>
                    <input name="address" value="{{ old('address', $student?->address) }}" placeholder="Masukkan alamat" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Kelompok</label>
                    <select name="group_id" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih kelompok</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" @selected((string) old('group_id', $student?->group_id) === (string) $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Domisili</label>
                    <select disabled class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm text-slate-700 outline-none">
                        <option>{{ $branch?->address ?: $branch?->name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Wali Santri</label>
                    <select name="guardian_id" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih wali santri</option>
                        @foreach ($guardians as $guardian)
                            <option value="{{ $guardian->id }}" @selected((string) old('guardian_id', $student?->guardian_id) === (string) $guardian->id)>{{ $guardian->name }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="status" value="{{ old('status', $student?->status ?? 'active') }}">
            </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="cursor-pointer rounded-md bg-slate-500 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-950">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
            </div>
        </section>
    </form>

    <script>
        const photoInput = document.getElementById('photo-input');
        const photoPreview = document.getElementById('photo-preview');
        const photoPlaceholder = document.getElementById('photo-placeholder');

        photoInput?.addEventListener('change', () => {
            const file = photoInput.files?.[0];
            if (! file) return;

            photoPreview.src = URL.createObjectURL(file);
            photoPreview.classList.remove('hidden');
            photoPlaceholder.classList.add('hidden');
        });
    </script>
</x-layouts.dashboard>
