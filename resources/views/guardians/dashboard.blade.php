@php
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
@endphp

<x-layouts.dashboard title="Dashboard" description="Ringkasan akademik santri Anda.">
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Periode</p>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-2xl font-bold text-slate-950">{{ $activePeriod?->academic_year ?: '-' }}</p>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $activePeriod?->semester ?: '-' }}</span>
                </div>
            </div>
            <a href="{{ route('guardians.students') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Santri</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['Students'] }} Santri</p>
            </a>
            <a href="{{ route('guardians.reports') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Rapor</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['Reports'] }} Rapor</p>
            </a>
            <a href="{{ route('guardians.lesson-scores') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Penilaian Terbaru</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['Assessments'] }} Nilai</p>
            </a>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Jadwal Akan Datang</h2>
                    <a href="{{ route('guardians.schedules') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                <div class="space-y-4">
                    @forelse ($upcomingSchedules as $schedule)
                        <div class="flex items-center justify-between gap-4 rounded-md border border-slate-200 bg-slate-50 p-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-950">{{ $schedule->all_groups ? 'Semua Kelompok' : $schedule->group?->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $schedule->start_date->format('d M Y') }} - {{ $schedule->end_date->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-600">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $schedule->total_meetings }} pertemuan</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada jadwal.</p>
                    @endforelse
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Presensi Terbaru</h2>
                    <a href="{{ route('guardians.attendance') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                @if ($latestAttendance)
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
                        <p class="text-sm text-slate-600">{{ $latestAttendance->attendance_date->format('d M Y') }} | Pertemuan ke-{{ $latestAttendance->meeting_number }} | {{ $latestAttendance->group?->name }}</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-4">
                        @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $status => $label)
                            <div class="rounded-md border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="text-xs font-semibold uppercase text-slate-500">{{ $label }}</p>
                                <p class="mt-1 text-2xl font-bold text-blue-950">{{ $attendanceSummary[$status] ?? 0 }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada data presensi.</p>
                @endif
            </article>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Rapor Terbaru</h2>
                    <a href="{{ route('guardians.reports') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full min-w-[500px] text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-4 py-3 font-bold">Santri</th>
                                <th class="px-4 py-3 font-bold">Periode</th>
                                <th class="px-4 py-3 font-bold">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm font-medium">{{ $report->student?->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $report->period?->name }} - {{ $report->period?->semester }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $report->report_date?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada rapor.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Nilai Terbaru</h2>
                    <a href="{{ route('guardians.lesson-scores') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full min-w-[500px] text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-4 py-3 font-bold">Santri</th>
                                <th class="px-4 py-3 font-bold">Jenis</th>
                                <th class="px-4 py-3 font-bold">Mata Pelajaran / Surah</th>
                                <th class="px-4 py-3 font-bold">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assessments as $assessment)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm font-medium">{{ $assessment->student?->name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($assessment->template?->name)
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-700">
                                                {{ $assessment->template->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @foreach($assessment->attributeValues as $attribute)
                                            {{ $attribute->attribute->name }} :
                                            {{ $attribute->value }}<br>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ number_format((float) $assessment->final_score, 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada nilai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </section>
</x-layouts.dashboard>