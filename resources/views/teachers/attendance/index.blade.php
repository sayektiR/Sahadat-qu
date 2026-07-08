@php
    $activeTab = in_array(request('tab'), ['input', 'latest', 'history'], true) ? request('tab') : 'input';
    $statusLabels = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];
    $statusClasses = [
        'hadir' => 'peer-checked:border-green-700 peer-checked:bg-green-700 peer-checked:text-white',
        'izin' => 'peer-checked:border-blue-700 peer-checked:bg-blue-700 peer-checked:text-white',
        'sakit' => 'peer-checked:border-yellow-500 peer-checked:bg-yellow-500 peer-checked:text-white',
        'alpha' => 'peer-checked:border-red-600 peer-checked:bg-red-600 peer-checked:text-white',
    ];
@endphp

<x-layouts.dashboard title="Presensi" description="Input dan pantau kehadiran santri pada kelompok yang diampu.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-end 2xl:justify-between">
            <div class="border-b border-slate-200">
                <button type="button" data-attendance-tab="input" onclick="showAttendanceTab('input')" class="cursor-pointer border-b-2 border-blue-950 px-4 py-3 text-sm font-semibold text-blue-950">
                    Presensi
                </button>
                <button type="button" data-attendance-tab="latest" onclick="showAttendanceTab('latest')" class="cursor-pointer border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-slate-700 hover:text-blue-950">
                    Presensi Baru
                </button>
                <button type="button" data-attendance-tab="history" onclick="showAttendanceTab('history')" class="cursor-pointer border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-slate-700 hover:text-blue-950">
                    Data Presensi
                </button>
            </div>

            <div class="w-full sm:w-auto">
                <input id="attendance-tab-input" form="attendance-filter-form" type="hidden" name="tab" value="{{ $activeTab }}">

                @if ($groups->count() > 1)
                    <form id="attendance-filter-form" method="GET" action="{{ route('teachers.attendance') }}" class="flex w-full items-center gap-2 sm:w-auto">
                        <select name="group_id" class="h-9 min-w-44 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" @selected((string) $selectedGroupId === (string) $group->id)>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="h-9 shrink-0 cursor-pointer rounded-md border border-slate-300 bg-white px-4 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Tampilkan</button>
                    </form>
                @elseif ($groups->count() === 1)
                    <div class="rounded-md border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kelompok yang diampu</p>
                        <p class="mt-1 text-sm font-bold text-slate-950">{{ $groups->first()->name }}</p>
                    </div>
                @else
                    <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Anda belum ditugaskan ke kelompok mana pun.
                    </div>
                @endif
            </div>
        </div>

        <div data-attendance-panel="input" class="space-y-5">
            <form method="POST" action="{{ route('teachers.attendance.store') }}">
                @csrf
                <input type="hidden" name="group_id" value="{{ $selectedGroupId }}">
                <input type="hidden" name="period_id" value="{{ $activePeriod?->id }}">
                <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}">

                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="grid gap-4 md:grid-cols-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">Tanggal</label>
                            <input name="attendance_date" type="date" value="{{ old('attendance_date', now()->format('Y-m-d')) }}" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">
                                Pertemuan
                            </label>
                            <input type="number" name="meeting_number" value="{{ old('meeting_number', $nextMeeting) }}" readonly class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">Jam Mulai</label>
                            <input name="start_time" type="time" value="{{ old('start_time', $schedule?->start_time ? substr($schedule->start_time, 0, 5) : '') }}" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">Jam Selesai</label>
                            <input name="end_time" type="time" value="{{ old('end_time', $schedule?->end_time ? substr($schedule->end_time, 0, 5) : '') }}" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        </div>
                        <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-semibold uppercase text-slate-500">Periode</p>
                            <p class="mt-1 text-sm font-bold text-slate-950">{{ $activePeriod?->name ?: '-' }}</p>
                        </div>
                    </div>
                </section>

                <div class="mt-5 overflow-x-auto rounded-lg bg-white shadow-sm">
                    <table class="w-full min-w-[980px] text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-6 py-5 font-bold">No.</th>
                                <th class="px-6 py-5 font-bold">Nama Lengkap</th>
                                <th class="px-6 py-5 font-bold">Kelompok</th>
                                <th class="px-6 py-5 font-bold">Kehadiran</th>
                                <th class="px-6 py-5 font-bold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-6 py-5 text-sm">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-5 text-sm font-medium">{{ $student->name }}</td>
                                    <td class="px-6 py-5 text-sm">{{ $student->group?->name ?: '-' }}</td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($statuses as $status)
                                                <label>
                                                    <input type="radio" name="students[{{ $student->id }}][status]" value="{{ $status }}" @checked(old("students.$student->id.status", 'hadir') === $status) class="peer sr-only">
                                                    <span class="{{ $statusClasses[$status] }} inline-flex h-9 cursor-pointer items-center rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 hover:border-blue-950">{{ $statusLabels[$status] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <input name="students[{{ $student->id }}][note]" value="{{ old("students.$student->id.note") }}" placeholder="Catatan opsional" class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada santri pada kelompok ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" @disabled($students->isEmpty() || ! $activePeriod) class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0B8C79] disabled:cursor-not-allowed disabled:bg-slate-300">
                        Simpan Presensi
                    </button>
                </div>
            </form>
        </div>

        <div data-attendance-panel="latest" class="hidden space-y-4">
            @if ($latestAttendance)
                <p class="text-sm font-semibold text-slate-700">
                    {{ $latestAttendance->attendance_date->format('d M Y') }}
                    <span class="mx-2">|</span>
                    {{ $latestAttendance->group?->name }}
                    <span class="mx-2">|</span>
                    Pertemuan ke-{{ $latestAttendance->meeting_number }}
                </p>
            @endif

            <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                <table class="w-full min-w-[820px] text-left">
                    <thead>
                        <tr class="bg-white text-sm text-slate-950">
                            <th class="px-6 py-5 font-bold">No.</th>
                            <th class="px-6 py-5 font-bold">Nama Lengkap</th>
                            <th class="px-6 py-5 font-bold">Kelompok</th>
                            <th class="px-6 py-5 font-bold">Status</th>
                            <th class="px-6 py-5 font-bold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestAttendance?->details ?? [] as $detail)
                            <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                <td class="px-6 py-5 text-sm">{{ $loop->iteration }}</td>
                                <td class="px-6 py-5 text-sm font-medium">{{ $detail->student?->name }}</td>
                                <td class="px-6 py-5 text-sm">{{ $detail->student?->group?->name ?: '-' }}</td>
                                <td class="px-6 py-5 text-sm">{{ $statusLabels[$detail->status] ?? ucfirst($detail->status) }}</td>
                                <td class="px-6 py-5 text-sm">{{ $detail->note ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada presensi terbaru pada kelompok ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div data-attendance-panel="history" class="hidden">
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                <table class="w-full min-w-[920px] text-left">
                    <thead>
                        <tr class="bg-white text-sm text-slate-950">
                            <th class="px-6 py-5 font-bold">Tanggal</th>
                            <th class="px-6 py-5 font-bold">Kelompok</th>
                            <th class="px-6 py-5 font-bold">Pertemuan</th>
                            <th class="px-6 py-5 font-bold">Jam</th>
                            <th class="px-6 py-5 font-bold">Ringkasan</th>
                            <th class="px-6 py-5 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            @php $summary = $attendance->details->countBy('status'); @endphp
                            <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                <td class="px-6 py-5 text-sm">{{ $attendance->attendance_date->format('d M Y') }}</td>
                                <td class="px-6 py-5 text-sm font-medium">{{ $attendance->group?->name }}</td>
                                <td class="px-6 py-5 text-sm">Ke-{{ $attendance->meeting_number }}</td>
                                <td class="px-6 py-5 text-sm">{{ $attendance->start_time ? substr($attendance->start_time, 0, 5) : '-' }} - {{ $attendance->end_time ? substr($attendance->end_time, 0, 5) : '-' }}</td>
                                <td class="px-6 py-5 text-sm">H: {{ $summary['hadir'] ?? 0 }}, I: {{ $summary['izin'] ?? 0 }}, S: {{ $summary['sakit'] ?? 0 }}, A: {{ $summary['alpha'] ?? 0 }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <button type="button" onclick="openDialog('view-attendance-{{ $attendance->id }}')" class="cursor-pointer text-blue-500 hover:text-blue-700" aria-label="Lihat presensi"><x-icon name="eye" /></button>
                                        <form method="POST" action="{{ route('teachers.attendance.destroy', $attendance) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                class="delete-btn text-red-600 hover:text-red-700"
                                                aria-label="Hapus Presensi"
                                            >
                                                <x-icon name="trash" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Data presensi belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $attendances->links() }}</div>
        </div>

        @foreach ($attendances as $attendance)
            <dialog id="view-attendance-{{ $attendance->id }}" class="management-dialog w-full max-w-4xl rounded-lg border border-slate-200 p-0 shadow-xl">
                <div class="bg-white">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-950">Detail Presensi</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $attendance->group?->name }} | Pertemuan ke-{{ $attendance->meeting_number }}</p>
                    </div>
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
                                        <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 text-sm font-medium">{{ $detail->student?->name }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $statusLabels[$detail->status] ?? ucfirst($detail->status) }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $detail->note ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                        <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
                    </div>
                </div>
            </dialog>
        @endforeach
    </section>

    <script>
        function showAttendanceTab(tab) {
            const tabInput = document.getElementById('attendance-tab-input');
            if (tabInput) {
                tabInput.value = tab;
            }

            document.querySelectorAll('[data-attendance-panel]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.attendancePanel !== tab);
            });

            document.querySelectorAll('[data-attendance-tab]').forEach((button) => {
                const active = button.dataset.attendanceTab === tab;
                button.classList.toggle('border-blue-950', active);
                button.classList.toggle('text-blue-950', active);
                button.classList.toggle('border-transparent', ! active);
                button.classList.toggle('text-slate-700', ! active);
            });
        }

        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }

        showAttendanceTab(@json($activeTab));
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
