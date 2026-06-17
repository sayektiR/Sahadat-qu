<x-layouts.dashboard title="Presensi" description="Lihat riwayat kehadiran santri Anda.">
    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('guardians.attendance') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[160px_170px_auto]">
                <select name="group_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Kelompok</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <select name="period_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Periode</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) request('period_id', $selectedPeriodId) === (string) $period->id)>{{ $period->name }} - {{ $period->semester }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
            </form>
        </div>

        @forelse ($attendances as $attendance)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-950">{{ $attendance->group?->name }}</h3>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ $attendance->attendance_date->format('d M Y') }}
                            <span class="mx-2">|</span>
                            Pertemuan ke-{{ $attendance->meeting_number }}
                            <span class="mx-2">|</span>
                            {{ $attendance->teacher?->name ?: '-' }}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full min-w-[500px] text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-4 py-3 font-bold">Nama Santri</th>
                                <th class="px-4 py-3 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendance->details->whereIn('student_id', $studentIds) as $detail)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm font-medium">{{ $detail->student?->name }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'hadir' => 'bg-green-100 text-green-700',
                                                'izin' => 'bg-yellow-100 text-yellow-700',
                                                'sakit' => 'bg-blue-100 text-blue-700',
                                                'alpha' => 'bg-red-100 text-red-700',
                                            ];
                                            $statusLabels = [
                                                'hadir' => 'Hadir',
                                                'izin' => 'Izin',
                                                'sakit' => 'Sakit',
                                                'alpha' => 'Alpha',
                                            ];
                                        @endphp
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$detail->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $statusLabels[$detail->status] ?? $detail->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
                Belum ada data presensi.
            </div>
        @endforelse

        {{ $attendances->links() }}
    </section>
</x-layouts.dashboard>
