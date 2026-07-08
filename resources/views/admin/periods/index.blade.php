<x-layouts.dashboard title="Periode" description="Kelola tahun ajaran dan semester aktif untuk data akademik.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.periods') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[200px_auto_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari periode" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
                <button type="button" onclick="openDialog('create-period')" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                    <x-icon name="plus" />
                    Tambah Periode
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[980px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Periode</th>
                        <th class="px-6 py-5 font-bold">Tahun Ajaran</th>
                        <th class="px-6 py-5 font-bold">Semester</th>
                        <th class="px-6 py-5 font-bold">Tanggal</th>
                        <th class="px-6 py-5 font-bold">Status</th>
                        <th class="px-6 py-5 font-bold">Dipakai</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $periods->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $period->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $period->academic_year }}</td>
                            <td class="px-6 py-5 text-sm">{{ $period->semester }}</td>
                            <td class="px-6 py-5 text-sm">{{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</td>
                            <td class="px-6 py-5 text-sm">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $period->is_active ? 'bg-blue-950 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $period->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </td>
                            <td class="px-6 py-5 text-sm">{{ $period->schedules_count + $period->assessments_count + $period->reports_count }} data</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="openDialog('edit-period-{{ $period->id }}')" class="cursor-pointer text-yellow-500 hover:text-yellow-700" aria-label="Edit periode"><x-icon name="pencil" /></button>
                                    <form method="POST" action="{{ route('admin.periods.destroy', $period) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            class="delete-btn text-red-500 hover:text-red-700"
                                            aria-label="Hapus periode"
                                        >
                                            <x-icon name="trash" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <dialog id="edit-period-{{ $period->id }}" class="management-dialog w-full max-w-2xl rounded-lg border border-slate-200 p-0 shadow-xl">
                            @include('admin.periods.partials.form', ['period' => $period, 'mode' => 'edit'])
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-500">Data periode belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $periods->links() }}
    </section>

    <dialog id="create-period" class="management-dialog w-full max-w-2xl rounded-lg border border-slate-200 p-0 shadow-xl">
        @include('admin.periods.partials.form', ['period' => null, 'mode' => 'create'])
    </dialog>

    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }

        @if ($errors->any() && old('_form') === 'create')
            openDialog('create-period');
        @endif

        @if ($errors->any() && old('_form') === 'edit' && old('_period_id'))
            openDialog('edit-period-{{ old('_period_id') }}');
        @endif
    </script>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Periode?',
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
