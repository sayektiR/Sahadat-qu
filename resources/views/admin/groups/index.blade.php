<x-layouts.dashboard title="Kelompok" description="Kelola kelompok belajar santri pada cabang aktif.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.groups') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[200px_auto]" id="filterForm">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" oninput="submitFilter()" placeholder="Cari kelompok" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div class="flex gap-2">
                    {{-- <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button> --}}
                    <button type="button" onclick="openDialog('create-group')" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Kelompok
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Kelompok</th>
                        <th class="px-6 py-5 font-bold">Deskripsi</th>
                        <th class="px-6 py-5 font-bold">Santri</th>
                        <th class="px-6 py-5 font-bold">Guru</th>
                        <th class="px-6 py-5 font-bold">Jadwal</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $groups->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium text-slate-950">{{ $group->name }}</td>
                            <td class="max-w-md px-6 py-5 text-sm text-slate-600">{{ $group->description ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $group->students_count }}</td>
                            <td class="px-6 py-5 text-sm">{{ $group->teachers_count }}</td>
                            <td class="px-6 py-5 text-sm">{{ $group->schedules_count }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="openDialog('view-group-{{ $group->id }}')" class="cursor-pointer text-blue-500 hover:text-blue-700" aria-label="Lihat kelompok"><x-icon name="eye" /></button>
                                    <button type="button" onclick="openDialog('edit-group-{{ $group->id }}')" class="cursor-pointer text-yellow-500 hover:text-yellow-700" aria-label="Edit kelompok"><x-icon name="pencil" /></button>
                                    <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            class="delete-btn text-red-600 hover:text-red-700">
                                            <x-icon name="trash" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <dialog id="view-group-{{ $group->id }}" class="management-dialog w-full max-w-xl rounded-lg border border-slate-200 p-0 shadow-xl">
                            <div class="bg-white">
                                <div class="border-b border-slate-200 px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-950">Detail Kelompok</h3>
                                </div>
                                <dl class="grid gap-4 p-6 text-sm sm:grid-cols-2">
                                    <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Nama Kelompok</dt><dd class="mt-1 text-slate-950">{{ $group->name }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Jumlah Santri</dt><dd class="mt-1">{{ $group->students_count }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Jumlah Guru</dt><dd class="mt-1">{{ $group->teachers_count }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Jumlah Jadwal</dt><dd class="mt-1">{{ $group->schedules_count }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Dibuat</dt><dd class="mt-1">{{ $group->created_at?->format('d M Y') }}</dd></div>
                                    <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Deskripsi</dt><dd class="mt-1">{{ $group->description ?: '-' }}</dd></div>
                                </dl>
                                <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                                    <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
                                </div>
                            </div>
                        </dialog>

                        <dialog id="edit-group-{{ $group->id }}" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
                            <form method="POST" action="{{ route('admin.groups.update', $group) }}" class="bg-white">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_form" value="edit">
                                <input type="hidden" name="_group_id" value="{{ $group->id }}">

                                <div class="border-b border-slate-200 px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-950">Edit Kelompok</h3>
                                    <p class="mt-1 text-sm text-slate-500">Perbarui nama dan deskripsi kelompok belajar.</p>
                                </div>
                                <div class="space-y-5 p-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-900">Nama Kelompok</label>
                                        <input name="name" value="{{ old('_form') === 'edit' && (string) old('_group_id') === (string) $group->id ? old('name') : $group->name }}" required placeholder="Masukkan nama kelompok" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-900">Deskripsi</label>
                                        <textarea name="description" rows="4" placeholder="Masukkan deskripsi kelompok" class="mt-2 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ old('_form') === 'edit' && (string) old('_group_id') === (string) $group->id ? old('description') : $group->description }}</textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
                                    <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
                                    <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">Simpan Perubahan</button>
                                </div>
                            </form>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">Data kelompok belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $groups->links() }}
    </section>

    <dialog id="create-group" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
        <form method="POST" action="{{ route('admin.groups.store') }}" class="bg-white">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-950">Tambah Kelompok</h3>
                <p class="mt-1 text-sm text-slate-500">Tambahkan kelompok belajar baru pada cabang aktif.</p>
            </div>
            <div class="space-y-5 p-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Kelompok</label>
                    <input name="name" value="{{ old('_form') === 'create' ? old('name') : '' }}" required placeholder="Masukkan nama kelompok" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Deskripsi</label>
                    <textarea name="description" rows="4" placeholder="Masukkan deskripsi kelompok" class="mt-2 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ old('_form') === 'create' ? old('description') : '' }}</textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
                <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">Simpan</button>
            </div>
        </form>
    </dialog>

    <script>
        let timer;

        function submitFilter() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); 
        }
    </script>

    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }

        @if ($errors->any() && old('_form') === 'create')
            openDialog('create-group');
        @endif

        @if ($errors->any() && old('_form') === 'edit' && old('_group_id'))
            openDialog('edit-group-{{ old('_group_id') }}');
        @endif
    </script>
    
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Kelompok?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-layouts.dashboard>
