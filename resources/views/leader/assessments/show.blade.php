<x-layouts.dashboard title="Detail Penilaian" description="Detail penilaian tanggal {{ $assessment->assessment_date->format('d M Y') }}.">
    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.assessments', request()->except('show')) }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">Detail Penilaian</h2>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Informasi Penilaian</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal</span>
                        <span class="font-medium text-slate-950">{{ $assessment->assessment_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jenis</span>
                        <span class="font-medium text-slate-950">{{ $assessment->assessment_type === 'hafalan' ? 'Hafalan' : 'Materi' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nilai Akhir</span>
                        <span class="font-medium text-slate-950">{{ number_format((float) $assessment->final_score, 1) }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Detail Subjek</h3>
                <div class="space-y-3 text-sm">
                    @if ($assessment->assessment_type === 'hafalan')
                        @if ($assessment->memorizationAssessment)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Surah</span>
                                <span class="font-medium text-slate-950">{{ $assessment->memorizationAssessment->surah }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Ayat</span>
                                <span class="font-medium text-slate-950">{{ $assessment->memorizationAssessment->from_ayah }} - {{ $assessment->memorizationAssessment->to_ayah }}</span>
                            </div>
                        @endif
                    @else
                        @if ($assessment->lessonAssessment)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Mata Pelajaran</span>
                                <span class="font-medium text-slate-950">{{ $assessment->lessonAssessment->subject?->name ?: '-' }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-slate-950">Data Santri & Guru</h3>
            <div class="grid gap-5 md:grid-cols-2">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Santri</span>
                        <span class="font-medium text-slate-950">{{ $assessment->student?->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cabang</span>
                        <span class="font-medium text-slate-950">{{ $assessment->group?->branch?->name ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelompok</span>
                        <span class="font-medium text-slate-950">{{ $assessment->group?->name ?: '-' }}</span>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Guru Penilai</span>
                        <span class="font-medium text-slate-950">{{ $assessment->teacher?->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Periode</span>
                        <span class="font-medium text-slate-950">{{ $assessment->period?->name }} - {{ $assessment->period?->semester }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.dashboard>
