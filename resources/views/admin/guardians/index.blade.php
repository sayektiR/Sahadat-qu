<x-layouts.dashboard title="Data Wali Santri" description="Kelola data wali santri dan hubungan dengan santri.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.guardians') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_auto]" id="filterForm">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />

                    <input
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari disini"
                        oninput="submitFilter()"
                        class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
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
                    <a href="{{ route('admin.guardians.create') }}" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Wali
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Lengkap</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guardians as $guardian)
                        @php
                            $studentNames = $guardian->students->pluck('name')->join(', ');
                            $groupNames = $guardian->students->pluck('group.name')->filter()->unique()->join(', ');
                        @endphp
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $guardians->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $guardian->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $studentNames ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $groupNames ?: '-' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="openDialog('view-guardian-{{ $guardian->id }}')" class="cursor-pointer text-blue-500 hover:text-blue-700" aria-label="Lihat wali"><x-icon name="eye" /></button>
                                    <a href="{{ route('admin.guardians.edit', $guardian) }}" class="cursor-pointer text-yellow-500 hover:text-yellow-700" aria-label="Edit wali"><x-icon name="pencil" /></a>
                                    <form method="POST" action="{{ route('admin.guardians.destroy', $guardian) }}" class="delete-form">
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

                        <x-admin.guardian-dialogs :guardian="$guardian" :student-names="$studentNames" :group-names="$groupNames" />
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Data wali santri belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $guardians->links() }}
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
                    title: 'Hapus Wali Santri?',
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
