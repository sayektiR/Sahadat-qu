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
                        <img id="photo-preview" src="{{ $student?->photo ? asset('storage/' . $student->photo) : '' }}" onclick="previewImage(this.src)" alt="Preview photo" class="{{ $student?->photo ? '' : 'hidden' }} h-full w-full object-cover">
                        <svg id="photo-placeholder" class="{{ $student?->photo ? 'hidden' : '' }}" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" opacity="0.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0ZM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632Z"/>
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
                        <input name="nis" id="nis" readonly value="{{ old('nis', $student?->nis) }}" placeholder="NIS otomatis dibuat oleh sistem" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900">NIK</label>
                        <input name="nik" id="nik" value="{{ old('nik', $student?->nik) }}" placeholder="Masukkan NIK" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Lengkap</label>
                    <input name="name" id="name" value="{{ old('name', $student?->name) }}" required placeholder="Masukkan nama lengkap" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Tempat</label>
                        <input name="birth_place" id="birth_place" value="{{ old('birth_place', $student?->birth_place) }}" placeholder="Masukkan tempat lahir" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Tanggal Lahir</label>
                        <input name="birth_date" id="birth_date" type="date" value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d')) }}" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih jenis kelamin</option>
                        <option value="female" @selected(old('gender', $student?->gender) === 'female')>Perempuan</option>
                        <option value="male" @selected(old('gender', $student?->gender) === 'male')>Laki-laki</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Alamat</label>
                    <input name="address" id="address" value="{{ old('address', $student?->address) }}" placeholder="Masukkan alamat" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Kelompok</label>
                    <select name="group_id" id="group_id" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih kelompok</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" @selected((string) old('group_id', $student?->group_id) === (string) $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Domisili</label>
                    <select name="branch_id" id="branch_id" disabled class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm text-slate-700 outline-none">
                        <option>{{ $branch?->address ?: $branch?->name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-900">Wali Santri</label>
                    <select id="guardian_id" name="guardian_id" class="mt-2 w-full"><option value=""></option></select>
                </div>

                <input type="hidden" name="status" value="{{ old('status', $student?->status ?? 'active') }}">
            </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0B8C79]">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
            </div>
        </section>
    </form>

    <dialog id="image-preview-dialog" class="m-auto border-0 bg-transparent p-0 backdrop:bg-black/80">
        <div class="relative flex items-center justify-center">

            <button type="button" onclick="closePreview()" class="absolute top-4 right-4 z-50 text-5xl leading-none text-white hover:opacity-75">
                &times;
            </button>

            <img
                id="preview-image"
                src=""
                alt="Preview"
                class="max-h-[85vh] w-auto max-w-[60vw] rounded-lg object-contain shadow-2xl"
            >
        </div>
    </dialog>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {

            $('#guardian_id').select2({
                width: '100%',
                placeholder: 'Cari wali...',
                allowClear: true,

                ajax: {
                    url: '/admin/guardians/search',
                    dataType: 'json',
                    delay: 250,

                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },

                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },

                    cache: true
                }
            });

        });
    </script>

    <script>
        const previewDialog = document.getElementById('image-preview-dialog');
        const previewImageElement = document.getElementById('preview-image');

        function previewImage(src) {
            previewImageElement.src = src;
            previewDialog.showModal();
        }

        function closePreview() {
            previewDialog.close();
        }

        previewDialog.addEventListener('click', (e) => {
            if (e.target === previewDialog) {
                previewDialog.close();
            }
        });
    </script>

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

    <script>
        function generateNis() {
            const nik = nikInput.value;
            const birthDate = birthDateInput.value;

            if (!nik || !birthDate) return;

            const date = new Date(birthDate);

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');

            const lastThreeNik = nik.slice(-3);

            nisInput.value = `SQ-${day}${month}${lastThreeNik}`;
        }
        nikInput.addEventListener('input', generateNis);
        birthDateInput.addEventListener('change', generateNis);

        generateNis();
    </script>

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 44px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            background: #f1f5f9 !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            line-height: 44px !important;
            padding-left: 12px !important;
            color:#0f172a;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            height:44px !important;
            right:10px !important;
        }
        .select2-dropdown{
            border:1px solid #cbd5e1 !important;
            border-radius:6px !important;
        }
        .select2-search__field{
            height:36px !important;
            border-radius:4px !important;
        }
        .select2-results__option{
            padding:10px 12px !important;
        }
</style>

    
</x-layouts.dashboard>
