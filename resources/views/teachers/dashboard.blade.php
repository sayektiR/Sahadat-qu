@php
    $groups = collect();

    if ($teacher->group) {
        $groups = \App\Models\Group::where('id', $teacher->group->id)
            ->withCount('students')
            ->orderBy('name')
            ->get();
    }

    $groupTotal = max(1, $groups->sum('students_count'));

    $groupColors = ['#172554', '#64748b', '#cbd5e1', '#94a3b8', '#0f172a'];
    $groupStops = [];
    $cursor = 0;

    foreach ($groups as $index => $group) {
        $portion = ($group->students_count / $groupTotal) * 100;
        $start = $cursor;
        $cursor += $portion;

        $groupStops[] =
            ($groupColors[$index % count($groupColors)])
            . ' '
            . $start
            . '% '
            . $cursor
            . '%';
    }
@endphp

<x-layouts.dashboard title="Dashboard" description="Ringkasan akademik kelompok yang diampu.">
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase text-slate-500">Periode</p>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $activePeriod?->semester ?: '-' }}
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-slate-950">
                    {{ $activePeriod?->academic_year ?: '-' }}
                </p>
            </div>
            <a href="{{ route('teachers.students') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Kelompok Diampu</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['My Groups'] }} Kelompok</p>
            </a>
            <a href="{{ route('teachers.students') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Santri</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['Students'] }} Santri</p>
            </a>
            <a href="{{ route('teachers.schedules') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Jadwal Aktif</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['Schedules'] }} Jadwal</p>
            </a>
            <a href="{{ route('teachers.assessments.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Penilaian</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['Assessments'] }} Penilaian</p>
            </a>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
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
                            <span class="text-2xl font-bold text-blue-950">{{ $groups->count() }}</span>
                        </div>
                    </div>
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-950">Jadwal Aktif</h2>
                        @if ($nextSchedule)
                            <p class="mt-1 text-sm text-slate-600">{{ $nextSchedule->group?->name }} | {{ substr($nextSchedule->start_time, 0, 5) }} - {{ substr($nextSchedule->end_time, 0, 5) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('teachers.schedules') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat jadwal</a>
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
                        <p class="mt-1 text-sm text-slate-600">{{ $latestAttendance->attendance_date->format('d M Y') }} | Pertemuan ke-{{ $latestAttendance->meeting_number }} | {{ $latestAttendance->group?->name }}</p>
                    @endif
                </div>
                <a href="{{ route('teachers.attendance') }}" class="text-sm font-semibold text-blue-950 hover:underline">Buka presensi</a>
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