@php
    $totalStudents = max(1, $stats['students']);
    $malePercent = round(($genderCounts['male'] / $totalStudents) * 100);
    $femalePercent = 100 - $malePercent;
    $groupTotal = max(1, $groups->sum('students_count'));
    $groupColors = ['#172554', '#64748b', '#cbd5e1', '#94a3b8', '#0f172a'];
    $groupStops = [];
    $cursor = 0;

    foreach ($groups as $index => $group) {
        $portion = ($group->students_count / $groupTotal) * 100;
        $start = $cursor;
        $cursor += $portion;
        $groupStops[] = ($groupColors[$index % count($groupColors)]) . ' ' . $start . '% ' . $cursor . '%';
    }

    $attendanceSummary = $latestAttendance?->details?->countBy('status') ?? collect();
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $scheduleDetails = $latestSchedule?->details?->groupBy('day') ?? collect();
@endphp

<x-layouts.dashboard title="Dashboard" description="Ringkasan akademik cabang aktif hari ini.">
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Periode</p>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-2xl font-bold text-slate-950">{{ $activePeriod?->academic_year ?: '-' }}</p>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $activePeriod?->semester ?: '-' }}</span>
                </div>
            </div>
            <a href="{{ route('admin.students') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Jumlah Santri</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['students'] }} Santri</p>
            </a>
            <a href="{{ route('admin.teachers') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Jumlah Guru</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['teachers'] }} Guru</p>
            </a>
            <a href="{{ route('admin.guardians') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Wali Santri</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['guardians'] }} Wali</p>
            </a>
            <a href="{{ route('admin.assessments') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Jumlah Materi</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['subjects'] }} Materi</p>
            </a>
        </div>

        <div class="grid gap-5 xl:grid-cols-[1fr_1fr_2fr]">
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-950">Santri</h2>
                <div class="mt-3 flex flex-wrap gap-4 text-xs font-semibold text-slate-500">
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-blue-950"></span>Laki-laki</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-slate-300"></span>Perempuan</span>
                </div>
                <div class="mt-8 flex justify-center">
                    <div class="h-44 w-44 rounded-full" style="background: conic-gradient(#172554 0% {{ $malePercent }}%, #cbd5e1 {{ $malePercent }}% 100%);"></div>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-3 text-center">
                    <div class="rounded-md bg-slate-50 p-3"><p class="text-xl font-bold text-blue-950">{{ $genderCounts['male'] }}</p><p class="text-xs text-slate-500">Laki-laki</p></div>
                    <div class="rounded-md bg-slate-50 p-3"><p class="text-xl font-bold text-blue-950">{{ $genderCounts['female'] }}</p><p class="text-xs text-slate-500">Perempuan</p></div>
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-950">Kelompok Santri</h2>
                <div class="mt-3 flex flex-wrap gap-3 text-xs font-semibold text-slate-500">
                    @foreach ($groups as $index => $group)
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full" style="background: {{ $groupColors[$index % count($groupColors)] }}"></span>{{ $group->name }}</span>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-center">
                    <div class="grid h-44 w-44 place-items-center rounded-full" style="background: conic-gradient({{ implode(', ', $groupStops) ?: '#cbd5e1 0% 100%' }});">
                        <div class="grid h-24 w-24 place-items-center rounded-full bg-white">
                            <span class="text-2xl font-bold text-blue-950">{{ $stats['groups'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 space-y-2">
                    @foreach ($groups->take(4) as $group)
                        <div class="flex justify-between text-sm"><span>{{ $group->name }}</span><strong>{{ $group->students_count }}</strong></div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Hafalan Dinilai per Bulan</h2>
                    <span class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600">{{ $activePeriod?->name ?: 'Semua Periode' }}</span>
                </div>
                <div class="mt-6 h-72">
                    <div class="flex h-60 items-end gap-4 border-b border-l border-slate-200 px-4">
                        @forelse ($memorizationChart as $item)
                            <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                                <div class="flex h-48 items-end gap-1">
                                    <div title="Laki-laki: {{ $item['male'] }}" class="w-4 rounded-t bg-blue-950" style="height: {{ max(6, ($item['male'] / $chartMax) * 180) }}px"></div>
                                    <div title="Perempuan: {{ $item['female'] }}" class="w-4 rounded-t bg-slate-300" style="height: {{ max(6, ($item['female'] / $chartMax) * 180) }}px"></div>
                                </div>
                                <span class="text-xs text-slate-500">{{ $item['label'] }}</span>
                            </div>
                        @empty
                            <div class="grid h-full w-full place-items-center text-sm text-slate-500">Belum ada data hafalan.</div>
                        @endforelse
                    </div>
                    <div class="mt-4 flex gap-4 text-xs font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-blue-950"></span>Laki-laki</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-slate-300"></span>Perempuan</span>
                    </div>
                </div>
            </article>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Daftar Guru</h2>
                    <a href="{{ route('admin.teachers') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                <div class="space-y-4">
                    @forelse ($teachers->take(6) as $teacher)
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="grid h-9 w-9 place-items-center rounded-full border border-slate-300 text-slate-700">
                                    <x-icon name="shield-user" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-950">{{ $teacher->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $teacher->phone ?: 'Telepon belum diisi' }}</p>
                                </div>
                            </div>
                            <p class="shrink-0 text-right text-sm text-slate-600">{{ $teacher->groups->pluck('name')->take(2)->join(', ') ?: '-' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Data guru belum tersedia.</p>
                    @endforelse
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-950">Jadwal Aktif</h2>
                        @if ($latestSchedule)
                            <p class="mt-1 text-sm text-slate-600">{{ $latestSchedule->group?->name }} | {{ substr($latestSchedule->start_time, 0, 5) }} - {{ substr($latestSchedule->end_time, 0, 5) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.schedules') }}" class="text-sm font-semibold text-blue-950 hover:underline">Kelola jadwal</a>
                </div>
                <div class="overflow-x-auto">
                    <div class="flex min-w-[640px] overflow-hidden rounded-md border border-slate-200">
                        @foreach ($days as $day)
                            <div class="min-w-0 flex-1 border-r border-slate-200 last:border-r-0">
                                <div class="bg-slate-50 px-3 py-3 text-center text-sm font-bold text-slate-900">{{ $day }}</div>
                                <div class="min-h-40 border-t border-slate-200">
                                    @forelse (($scheduleDetails[$day] ?? collect())->sortBy('order_number') as $detail)
                                        <div class="border-b border-slate-100 px-2 py-3 text-center text-xs text-slate-700 last:border-b-0">{{ $detail->subject?->name ?: $detail->material_name }}</div>
                                    @empty
                                        <div class="px-2 py-8 text-center text-xs text-slate-400">-</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        </div>

        <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Presensi Terbaru</h2>
                    @if ($latestAttendance)
                        <p class="mt-1 text-sm text-slate-600">{{ $latestAttendance->attendance_date->format('d M Y') }} | Pertemuan ke-{{ $latestAttendance->meeting_number }} | {{ $latestAttendance->teacher?->name ?: '-' }}</p>
                    @endif
                </div>
                <a href="{{ route('admin.attendance') }}" class="text-sm font-semibold text-blue-950 hover:underline">Buka presensi</a>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-4">
                @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $status => $label)
                    <div class="rounded-md border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-bold text-blue-950">{{ $attendanceSummary[$status] ?? 0 }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
</x-layouts.dashboard>
