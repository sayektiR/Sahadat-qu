@php
    $isEdit = $mode === 'edit';
    $pageTitle = $isEdit ? 'Edit Guru' : 'Tambah Guru';
    $action = $isEdit ? route('admin.teachers.update', $teacher) : route('admin.teachers.store');
@endphp

<x-layouts.dashboard :title="$pageTitle" description="Lengkapi data guru dan akun login pengajar.">
    <form id="teacher-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="mb-6">
            <nav class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-950">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.teachers') }}" class="hover:text-blue-950">Data Guru</a>
                <span>/</span>
                <span class="font-medium text-slate-900">{{ $pageTitle }}</span>
            </nav>
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
                        <img id="photo-preview" src="{{ $teacher?->photo ? asset('storage/' . $teacher->photo) : '' }}" onclick="previewImage(this.src)" alt="Preview photo" class="{{ $teacher?->photo ? '' : 'hidden' }} h-full w-full object-cover">
                        <svg id="photo-placeholder" class="{{ $teacher?->photo ? 'hidden' : '' }}" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" opacity="0.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0ZM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632Z"/>
                        </svg>
                    </div>
                    <input id="photo-input" name="photo" type="file" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('photo-input').click()" class="mt-3 h-8 w-40 cursor-pointer rounded-md border border-slate-500 bg-white text-xs font-medium text-slate-700 hover:border-blue-950 hover:text-blue-950">
                        Ubah Photo
                    </button>
                </aside>

                <div class="space-y-5">
                    <h3 class="text-xl font-bold text-slate-950">Data Guru</h3>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Nama Lengkap</label>
                        <input name="name" value="{{ old('name', $teacher?->name) }}" required placeholder="Masukkan nama lengkap" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Email</label>
                        <input name="email" type="email" required value="{{ old('email', $teacher?->user?->email) }}" placeholder="Masukkan email" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">No. Telepon</label>
                            <input name="phone" value="{{ old('phone', $teacher?->phone) }}" placeholder="Masukkan nomor telepon" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">Jenis Kelamin</label>
                            <select name="gender" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="female" @selected(old('gender', $teacher?->gender) === 'female')>Perempuan</option>
                                <option value="male" @selected(old('gender', $teacher?->gender) === 'male')>Laki-laki</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Kelompok</label>
                        <select name="group_id" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                            <option value="">Pilih Kelompok</option>

                            @foreach($groups as $group)
                                <option value="{{ $group->id }}"
                                    @selected(old('group_id', $teacher?->group_id) == $group->id)>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Alamat</label>
                        <input name="address" value="{{ old('address', $teacher?->address) }}" placeholder="Masukkan alamat" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900">Status</label>
                        <select name="status" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                            <option value="active" @selected(old('status', $teacher?->status ?? 'active') === 'active')>Aktif</option>
                            <option value="inactive" @selected(old('status', $teacher?->status) === 'inactive')>Tidak Aktif</option>
                        </select>
                    </div>
                    @unless ($isEdit)
                        <p class="text-sm text-slate-500">Jika email diisi, akun guru dibuat dengan password default <span class="font-semibold">password123</span>.</p>
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
</x-layouts.dashboard>
