<x-layouts.dashboard title="Detail Rapor" description="Rapor santri {{ $report->student?->name }}.">
    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.reports', request()->except('show')) }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">Rapor {{ $report->student?->name }}</h2>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Informasi Rapor</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Rapor</span>
                        <span class="font-medium text-slate-950">{{ $report->report_date?->format('d M Y') ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Periode</span>
                        <span class="font-medium text-slate-950">{{ $report->period?->name }} - {{ $report->period?->semester }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Guru Wali</span>
                        <span class="font-medium text-slate-950">{{ $report->homeroomTeacher?->name ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Data Santri</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama</span>
                        <span class="font-medium text-slate-950">{{ $report->student?->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cabang</span>
                        <span class="font-medium text-slate-950">{{ $report->branch?->name ?: $report->student?->branch?->name ?: $report->student?->group?->branch?->name ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelompok</span>
                        <span class="font-medium text-slate-950">{{ $report->student?->group?->name ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Ringkasan Presensi</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Hadir</span>
                        <span class="font-medium text-slate-950">{{ $attendanceSummary['hadir'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Izin</span>
                        <span class="font-medium text-slate-950">{{ $attendanceSummary['izin'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Sakit</span>
                        <span class="font-medium text-slate-950">{{ $attendanceSummary['sakit'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alpha</span>
                        <span class="font-medium text-slate-950">{{ $attendanceSummary['alpha'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($assessments->isNotEmpty())
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-slate-950">
                Data Penilaian
            </h3>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full min-w-[900px] text-left">
                    <thead>
                        <tr class="bg-white text-sm text-slate-950">
                            <th class="px-4 py-3 font-bold">Tanggal</th>
                            <th class="px-4 py-3 font-bold">Template</th>
                            <th class="px-4 py-3 font-bold">Aspek Penilaian</th>
                            <th class="px-4 py-3 font-bold">Nilai Akhir</th>
                            <th class="px-4 py-3 font-bold">Predikat</th>
                            <th class="px-4 py-3 font-bold">Catatan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($assessments as $assessment)
                            <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                <td class="px-4 py-3 text-sm">
                                    {{ $assessment->assessment_date->format('d M Y') }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $assessment->template?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    @forelse ($assessment->scorings as $scoring)
                                        <div>
                                            <strong>{{ $scoring->aspect?->name ?? '-' }}</strong> :
                                            {{ $scoring->value }}
                                        </div>
                                    @empty
                                        -
                                    @endforelse
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ number_format((float) $assessment->final_score, 1) }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $assessment->predicate ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $assessment->note ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-slate-200 bg-white p-6 text-center text-sm text-slate-500 shadow-sm">
            Belum ada data penilaian.
        </div>
    @endif

    </section>
</x-layouts.dashboard>
