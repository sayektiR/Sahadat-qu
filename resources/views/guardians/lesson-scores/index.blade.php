<x-layouts.dashboard title="Nilai" description="Lihat nilai materi dan hafalan santri Anda.">
    <section class="space-y-6">
        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-end 2xl:justify-between">
            <div class="border-b border-slate-200">
                <a href="{{ route('guardians.lesson-scores', array_merge(request()->except('assessment_type', 'page'), ['assessment_type' => 'materi', 'period_id' => request('period_id', $selectedPeriodId)])) }}" class="{{ $assessmentType === 'materi' ? 'border-blue-950 text-blue-950' : 'border-transparent text-slate-700' }} inline-flex border-b-2 px-4 py-3 text-sm font-semibold hover:text-blue-950">Materi</a>
                <a href="{{ route('guardians.lesson-scores', array_merge(request()->except('assessment_type', 'subject_id', 'page'), ['assessment_type' => 'hafalan', 'period_id' => request('period_id', $selectedPeriodId)])) }}" class="{{ $assessmentType === 'hafalan' ? 'border-blue-950 text-blue-950' : 'border-transparent text-slate-700' }} inline-flex border-b-2 px-4 py-3 text-sm font-semibold hover:text-blue-950">Hafalan</a>
            </div>
            <form method="GET" action="{{ route('guardians.lesson-scores') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto {{ $assessmentType === 'materi' ? 'xl:grid-cols-[160px_170px_170px_auto]' : 'xl:grid-cols-[160px_170px_auto]' }}">
                <input type="hidden" name="assessment_type" value="{{ $assessmentType }}">
                <select name="group_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Kelompok</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)">{{ $group->name }}</option>
                    @endforeach
                </select>
                @if ($assessmentType === 'materi')
                    <select name="subject_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) request('subject_id') === (string) $subject->id)">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="period_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Periode</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) request('period_id', $selectedPeriodId) === (string) $period->id)">{{ $period->name }} - {{ $period->semester }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full text-left" style="min-width: {{ $assessmentType === 'hafalan' ? 900 : 700 }}px;">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        @if ($assessmentType === 'hafalan')
                            <th class="px-6 py-5 font-bold">Jenis</th>
                            <th class="px-6 py-5 font-bold">Surah</th>
                            <th class="px-6 py-5 font-bold">Ayat</th>
                        @else
                            <th class="px-6 py-5 font-bold">Mata Pelajaran</th>
                        @endif
                        <th class="px-6 py-5 font-bold">Tanggal</th>
                        <th class="px-6 py-5 font-bold">Nilai</th>
                        <th class="px-6 py-5 font-bold">Predikat</th>
                        <th class="px-6 py-5 font-bold">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $assessments->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $assessment->student?->name }}</td>
                            @if ($assessmentType === 'hafalan')
                                <td class="px-6 py-5 text-sm">{{ $assessment->memorizationAssessment?->memorization_type ?: '-' }}</td>
                                <td class="px-6 py-5 text-sm">{{ $assessment->memorizationAssessment?->surah ?: '-' }}</td>
                                <td class="px-6 py-5 text-sm">
                                    @if ($assessment->memorizationAssessment?->from_ayah && $assessment->memorizationAssessment?->to_ayah)
                                        {{ $assessment->memorizationAssessment->from_ayah }} - {{ $assessment->memorizationAssessment->to_ayah }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @else
                                <td class="px-6 py-5 text-sm">{{ $assessment->lessonAssessment?->subject?->name ?: '-' }}</td>
                            @endif
                            <td class="px-6 py-5 text-sm">{{ $assessment->assessment_date?->format('d M Y') ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm font-semibold text-blue-950">{{ number_format((float) $assessment->final_score, 1) }}</td>
                            <td class="px-6 py-5 text-sm">
                                @php
                                    $predicateColors = [
                                        'Mumtaz' => 'bg-green-100 text-green-700',
                                        'Jayyid Jiddan' => 'bg-blue-100 text-blue-700',
                                        'Jayyid' => 'bg-yellow-100 text-yellow-700',
                                        'Perlu Mengulang' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $predicateColors[$assessment->predicate] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $assessment->predicate ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $assessmentType === 'hafalan' ? 9 : 7 }}" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada nilai {{ $assessmentType === 'hafalan' ? 'hafalan' : 'mata pelajaran' }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $assessments->links() }}
    </section>
</x-layouts.dashboard>
