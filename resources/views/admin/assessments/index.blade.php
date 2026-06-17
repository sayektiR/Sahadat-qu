@php
    $typeLabels = ['materi' => 'Materi', 'hafalan' => 'Hafalan'];
    $currentType = request('assessment_type', '');
@endphp

<x-layouts.dashboard title="Penilaian" description="Pantau hasil penilaian materi dan hafalan santri.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Penilaian</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $totalCount }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Materi</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $lessonCount }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Hafalan</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $memorizationCount }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Rata-rata Nilai</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $averageScore ? number_format($averageScore, 1) : '-' }}</p>
            </div>
        </div>

        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-end 2xl:justify-between">
            <div class="border-b border-slate-200">
                <a href="{{ route('admin.assessments', request()->except('assessment_type', 'page')) }}" class="{{ $currentType === '' ? 'border-blue-950 text-blue-950' : 'border-transparent text-slate-700' }} inline-flex border-b-2 px-4 py-3 text-sm font-semibold hover:text-blue-950">Semua</a>
                <a href="{{ route('admin.assessments', array_merge(request()->except('page'), ['assessment_type' => 'materi'])) }}" class="{{ $currentType === 'materi' ? 'border-blue-950 text-blue-950' : 'border-transparent text-slate-700' }} inline-flex border-b-2 px-4 py-3 text-sm font-semibold hover:text-blue-950">Materi</a>
                <a href="{{ route('admin.assessments', array_merge(request()->except('page'), ['assessment_type' => 'hafalan'])) }}" class="{{ $currentType === 'hafalan' ? 'border-blue-950 text-blue-950' : 'border-transparent text-slate-700' }} inline-flex border-b-2 px-4 py-3 text-sm font-semibold hover:text-blue-950">Hafalan</a>
            </div>

            <form method="GET" action="{{ route('admin.assessments') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_160px_auto]">
                @if ($currentType)
                    <input type="hidden" name="assessment_type" value="{{ $currentType }}">
                @endif
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari santri/guru" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
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
                        <option value="{{ $period->id }}" @selected((string) request('period_id') === (string) $period->id)>{{ $period->name }} - {{ $period->semester }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[1120px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Tanggal</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Jenis</th>
                        <th class="px-6 py-5 font-bold">Detail</th>
                        <th class="px-6 py-5 font-bold">Nilai</th>
                        <th class="px-6 py-5 font-bold">Predikat</th>
                        <th class="px-6 py-5 font-bold">Guru</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                        @php
                            $detailText = $assessment->assessment_type === 'materi'
                                ? ($assessment->lessonAssessment?->subject?->name ?: '-')
                                : trim(($assessment->memorizationAssessment?->surah ?: '-') . ' ayat ' . ($assessment->memorizationAssessment?->from_ayah ?: '-') . '-' . ($assessment->memorizationAssessment?->to_ayah ?: '-'));
                        @endphp
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $assessments->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->assessment_date?->format('d M Y') }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $assessment->student?->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $typeLabels[$assessment->assessment_type] ?? $assessment->assessment_type }}</td>
                            <td class="px-6 py-5 text-sm">{{ $detailText }}</td>
                            <td class="px-6 py-5 text-sm font-semibold text-blue-950">{{ number_format((float) $assessment->final_score, 1) }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->predicate ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->teacher?->name ?: '-' }}</td>
                            <td class="px-6 py-5">
                                <button type="button" onclick="openDialog('view-assessment-{{ $assessment->id }}')" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat penilaian"><x-icon name="eye" /></button>
                            </td>
                        </tr>

                        <dialog id="view-assessment-{{ $assessment->id }}" class="management-dialog w-full max-w-3xl rounded-lg border border-slate-200 p-0 shadow-xl">
                            <div class="bg-white">
                                <div class="border-b border-slate-200 px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-950">Detail Penilaian</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $assessment->student?->name }} | {{ $typeLabels[$assessment->assessment_type] ?? $assessment->assessment_type }}</p>
                                </div>

                                <div class="grid gap-4 p-6 text-sm sm:grid-cols-2">
                                    <div><dt class="font-semibold text-slate-500">Tanggal</dt><dd class="mt-1">{{ $assessment->assessment_date?->format('d M Y') }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Periode</dt><dd class="mt-1">{{ $assessment->period?->name }} - {{ $assessment->period?->semester }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Santri</dt><dd class="mt-1">{{ $assessment->student?->name }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Kelompok</dt><dd class="mt-1">{{ $assessment->group?->name ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Guru</dt><dd class="mt-1">{{ $assessment->teacher?->name ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Nilai Akhir</dt><dd class="mt-1 font-bold text-blue-950">{{ number_format((float) $assessment->final_score, 1) }} - {{ $assessment->predicate ?: '-' }}</dd></div>

                                    @if ($assessment->assessment_type === 'materi')
                                        <div><dt class="font-semibold text-slate-500">Mata Pelajaran</dt><dd class="mt-1">{{ $assessment->lessonAssessment?->subject?->name ?: '-' }}</dd></div>
                                        <div><dt class="font-semibold text-slate-500">Skor Materi</dt><dd class="mt-1">{{ number_format((float) $assessment->lessonAssessment?->score, 1) }}</dd></div>
                                    @else
                                        <div><dt class="font-semibold text-slate-500">Jenis Hafalan</dt><dd class="mt-1">{{ $assessment->memorizationAssessment?->memorization_type ?: '-' }}</dd></div>
                                        <div><dt class="font-semibold text-slate-500">Surah</dt><dd class="mt-1">{{ $assessment->memorizationAssessment?->surah ?: '-' }}</dd></div>
                                        <div><dt class="font-semibold text-slate-500">Ayat</dt><dd class="mt-1">{{ $assessment->memorizationAssessment?->from_ayah }} - {{ $assessment->memorizationAssessment?->to_ayah }}</dd></div>
                                        <div><dt class="font-semibold text-slate-500">Kelancaran</dt><dd class="mt-1">{{ number_format((float) $assessment->memorizationAssessment?->fluency_score, 1) }}</dd></div>
                                        <div><dt class="font-semibold text-slate-500">Tajwid</dt><dd class="mt-1">{{ number_format((float) $assessment->memorizationAssessment?->tajwid_score, 1) }}</dd></div>
                                        <div><dt class="font-semibold text-slate-500">Makhraj</dt><dd class="mt-1">{{ number_format((float) $assessment->memorizationAssessment?->makhraj_score, 1) }}</dd></div>
                                    @endif

                                    <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Catatan</dt><dd class="mt-1">{{ $assessment->note ?: '-' }}</dd></div>
                                </div>

                                <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                                    <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-sm text-slate-500">Data penilaian belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $assessments->links() }}
    </section>

    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }
    </script>
</x-layouts.dashboard>
