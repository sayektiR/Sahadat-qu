@php
    $totalStudents = max(1, $totalStudents);
    $malePercent = round(($genderCounts['male'] / $totalStudents) * 100);
    $femalePercent = 100 - $malePercent;
@endphp

<x-layouts.dashboard title="Rapor" description="Kelola dan cetak laporan perkembangan santri kelompok yang diampu.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Periode Aktif</p>
                <p class="mt-2 text-2xl font-bold text-slate-950">{{ $activePeriod?->academic_year ?: '-' }}</p>
            </div>
            <a href="{{ route('teachers.students') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Santri</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $totalStudents }} Santri</p>
            </a>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Rapor</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $reports->total() }} Rapor</p>
            </div>
            <a href="{{ route('teachers.assessments.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-blue-950">
                <p class="text-xs font-semibold uppercase text-slate-500">Kelompok</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $groups->count() }} Kelompok</p>
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
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Rata-rata penilaian</h2>
                    <span class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600">{{ $activePeriod?->name ?: 'Semua Periode' }}</span>
                </div>
                <div class="mt-6 h-72">
                    <div class="flex h-60 items-end gap-4 border-b border-l border-slate-200 px-4">
                        
                    </div>
                    <div class="mt-4 flex gap-4 text-xs font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-blue-950"></span>Laki-laki</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-slate-300"></span>Perempuan</span>
                    </div>
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-950">Presensi Terbaru</h2>
                        @if ($latestAttendance)
                            <p class="mt-1 text-sm text-slate-600">{{ $latestAttendance->attendance_date->format('d M Y') }} | Pertemuan ke-{{ $latestAttendance->meeting_number }}</p>
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
        </div>

        <div class="flex justify-end">
            <form method="GET" action="{{ route('teachers.reports') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari santri" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
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

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[960px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Lengkap</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Nama Wali</th>
                        <th class="px-6 py-5 font-bold">Periode</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $reports->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $report->student?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->student?->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->student?->guardian?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->period?->name }} - {{ $report->period?->semester }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('teachers.reports.show', $report) }}" target="_blank" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat rapor"><x-icon name="eye" /></a>
                                    <a href="{{ route('teachers.reports.show', $report) }}?print=1" target="_blank" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Unduh PDF"><x-icon name="download" /></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Data rapor belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $reports->links() }}
    </section>
</x-layouts.dashboard>
