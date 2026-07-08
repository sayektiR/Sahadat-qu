@php
    $statusLabels = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpha' => 'Alpha',
    ];
@endphp

<x-layouts.dashboard title="Presensi" description="Pantau dan kelola kehadiran santri setiap pertemuan.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-end 2xl:justify-between">
            <div class="border-b border-slate-200">
                
                <button type="button" data-attendance-tab="history" onclick="showAttendanceTab('history')" class="cursor-pointer border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-slate-700 hover:text-blue-950">
                    Data Presensi
                </button>
            </div>

            <form method="GET" action="{{ route('admin.attendance') }}" id="filterForm" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari disini" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <select name="group_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Kelompok</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    
                </div>
            </form>
        </div>


        <div data-attendance-panel="history">
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                <table class="w-full min-w-[980px] text-left">
                    <thead>
                        <tr class="bg-white text-sm text-slate-950">
                            <th class="px-6 py-5 font-bold">Tanggal</th>
                            <th class="px-6 py-5 font-bold">Kelompok</th>
                            <th class="px-6 py-5 font-bold">Pertemuan</th>
                            <th class="px-6 py-5 font-bold">Jam</th>
                            <th class="px-6 py-5 font-bold">Guru</th>
                            <th class="px-6 py-5 font-bold">Ringkasan</th>
                            <th class="px-6 py-5 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            @php
                                $summary = $attendance->details->countBy('status');
                            @endphp
                            <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                <td class="px-6 py-5 text-sm">{{ $attendance->attendance_date->format('d M Y') }}</td>
                                <td class="px-6 py-5 text-sm font-medium">{{ $attendance->group?->name }}</td>
                                <td class="px-6 py-5 text-sm">Ke-{{ $attendance->meeting_number }}</td>
                                <td class="px-6 py-5 text-sm">{{ $attendance->start_time ? substr($attendance->start_time, 0, 5) : '-' }} - {{ $attendance->end_time ? substr($attendance->end_time, 0, 5) : '-' }}</td>
                                <td class="px-6 py-5 text-sm">{{ $attendance->teacher?->name ?: '-' }}</td>
                                <td class="px-6 py-5 text-sm">
                                    H: {{ $summary['hadir'] ?? 0 }},
                                    I: {{ $summary['izin'] ?? 0 }},
                                    S: {{ $summary['sakit'] ?? 0 }},
                                    A: {{ $summary['alpha'] ?? 0 }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <button type="button" onclick="openDialog('view-attendance-{{ $attendance->id }}')" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat presensi"><x-icon name="eye" /></button>
                                        
                                        <form method="POST" action="{{ route('admin.attendance.destroy', $attendance) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn text-red-600 hover:text-red-700"><x-icon name="trash" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">Data presensi belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $attendances->links() }}
            </div>
        </div>

        @foreach ($attendances as $attendance)
            <dialog id="view-attendance-{{ $attendance->id }}" class="management-dialog w-full max-w-4xl rounded-lg border border-slate-200 p-0 shadow-xl">
                <div class="bg-white">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-950">Detail Presensi</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $attendance->group?->name }} | Pertemuan ke-{{ $attendance->meeting_number }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
    @csrf
    @method('PUT')

    <div class="max-h-[65vh] overflow-auto p-6">
        <table class="w-full min-w-[720px] text-left">
            <thead>
                <tr class="text-sm text-slate-950">
                    <th class="border-b border-slate-200 px-4 py-3">No.</th>
                    <th class="border-b border-slate-200 px-4 py-3">Santri</th>
                    <th class="border-b border-slate-200 px-4 py-3">Status</th>
                    <th class="border-b border-slate-200 px-4 py-3">Catatan</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($attendance->details->sortBy('student.name') as $detail)
                    <tr class="{{ $loop->odd ? 'bg-slate-50' : 'bg-white' }}">
                        <td class="px-4 py-3 text-sm">
                            {{ $loop->iteration }}
                        </td>

                        <td class="px-4 py-3 text-sm font-medium">
                            {{ $detail->student?->name }}
                        </td>

                        <td class="px-4 py-3">
                            <select
                                name="details[{{ $detail->id }}][status]"
                                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">

                                <option value="hadir" @selected($detail->status == 'hadir')>
                                    Hadir
                                </option>

                                <option value="izin" @selected($detail->status == 'izin')>
                                    Izin
                                </option>

                                <option value="sakit" @selected($detail->status == 'sakit')>
                                    Sakit
                                </option>

                                <option value="alpha" @selected($detail->status == 'alpha')>
                                    Alpha
                                </option>
                            </select>
                        </td>

                        <td class="px-4 py-3">
                            <input
                                type="text"
                                name="details[{{ $detail->id }}][note]"
                                value="{{ $detail->note }}"
                                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">

        <button
            type="submit"
            class="rounded-md bg-blue-950 px-4 py-2 text-white">
            Simpan
        </button>

        <button
            type="button"
            onclick="closeDialog(this)"
            class="rounded-md border border-slate-300 px-4 py-2">
            Tutup
        </button>

    </div>
</form>
                </div>
            </dialog>
        @endforeach
    </section>

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
                    title: 'Hapus Presensi?',
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
