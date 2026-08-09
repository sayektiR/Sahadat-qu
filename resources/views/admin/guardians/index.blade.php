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
                    <a href="{{ route('admin.guardians.create') }}"
                    class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Wali
                    </a>
                    <a href="{{ route('admin.guardians.import.template') }}"
                    class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        Download Template
                    </a>
                    <button
                        type="button"
                        onclick="openDialog('importGuardianModal')"
                        class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md bg-blue-950 px-3 text-xs font-semibold text-white hover:bg-blue-900">
                        Import Wali
                    </button>

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

    @if (session('import_error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal',
                    text: @json(session('import_error')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#1e3a8a'
                });
            });
        </script>
    @endif

    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('status')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#1e3a8a'
                });
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Modal Import Wali Santri --}}
    <dialog id="importGuardianModal" class="fixed left-1/2 top-1/2 m-0 w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/50">

        <div class="bg-white">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Import Wali Santri
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Import data wali santri menggunakan file Excel.
                    </p>
                </div>

                <button type="button" onclick="closeDialog(this)" class="rounded-md p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                    <span class="text-xl">&times;</span>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.guardians.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-5 px-6 py-6">

                    {{-- Download Template --}}
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">

                        <p class="text-sm font-semibold text-blue-950">
                            Gunakan Template Excel
                        </p>

                        <p class="mt-1 text-xs text-blue-800">
                            Pastikan format kolom sesuai dengan template
                            sebelum melakukan import.
                        </p>

                        <a
                            href="{{ route('admin.guardians.import.template') }}"
                            class="mt-3 inline-flex text-xs font-semibold text-blue-700 hover:text-blue-900">

                            Download Template

                        </a>

                    </div>


                    {{-- Upload File --}}
                    <div>

                        <label for="guardian-import-file" class="mb-2 block text-sm font-semibold text-slate-700">
                            File Excel
                        </label>

                        <input id="guardian-import-file" type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-700">
                        <p class="mt-2 text-xs text-slate-500">
                            Format yang diperbolehkan: XLSX, XLS, atau CSV.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        onclick="closeDialog(this)"
                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="rounded-md bg-blue-950 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-900">

                        Import Data

                    </button>

                </div>

            </form>

        </div>

    </dialog>
</x-layouts.dashboard>
