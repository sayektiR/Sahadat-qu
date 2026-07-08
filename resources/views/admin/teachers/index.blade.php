<x-layouts.dashboard title="Data Guru" description="Kelola data guru dan akun login pengajar.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.teachers') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_150px_auto]" id="filterForm">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" oninput="submitFilter()" value="{{ request('search') }}" placeholder="Cari guru" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <select name="group_id" onchange="submitFilter()" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Kelompok</option>

                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}"
                            @selected((string) request('group_id') === (string) $group->id)>
                            {{ $group->name }}
                        </option>
                    @endforeach

                </select>
                <div class="flex gap-2">
                    {{-- <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button> --}}
                    <a href="{{ route('admin.teachers.create') }}" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Guru
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[960px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Guru</th>
                        <th class="px-6 py-5 font-bold">Email</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Telepon</th>
                        <th class="px-6 py-5 font-bold">Status</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $teachers->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $teacher->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $teacher->user?->email ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $teacher->group?->name ?? '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $teacher->phone ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $teacher->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="openDialog('view-teacher-{{ $teacher->id }}')" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat guru"><x-icon name="eye" /></button>
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Edit guru"><x-icon name="pencil" /></a>
                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="delete-btn cursor-pointer text-slate-900 hover:text-red-600" aria-label="Hapus guru"><x-icon name="trash" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <dialog id="view-teacher-{{ $teacher->id }}" class="management-dialog w-full max-w-xl rounded-lg border border-slate-200 p-0 shadow-xl">
                            <div class="bg-white">
                                <div class="border-b border-slate-200 px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-950">Detail Guru</h3>
                                </div>
                                @if ($teacher->photo)
                                    <div class="border-b border-slate-200 px-6 py-5">
                                        <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Foto {{ $teacher->name }}" class="h-28 w-28 rounded-md object-cover">
                                    </div>
                                @endif
                                <dl class="grid gap-4 p-6 text-sm sm:grid-cols-2">
                                    <div><dt class="font-semibold text-slate-500">Nama</dt><dd class="mt-1">{{ $teacher->name }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Email</dt><dd class="mt-1">{{ $teacher->user?->email ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Kelompok</dt><dd class="mt-1">{{ $teacher->group?->name ?? '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Telepon</dt><dd class="mt-1">{{ $teacher->phone ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Jenis Kelamin</dt><dd class="mt-1">{{ $teacher->gender === 'female' ? 'Perempuan' : ($teacher->gender === 'male' ? 'Laki-laki' : '-') }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Status</dt><dd class="mt-1">{{ $teacher->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</dd></div>
                                    <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Alamat</dt><dd class="mt-1">{{ $teacher->address ?: '-' }}</dd></div>
                                </dl>
                                <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                                    <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Data guru belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $teachers->links() }}
    </section>

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
    </script>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Guru?',
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
