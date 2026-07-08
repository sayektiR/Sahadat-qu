<x-layouts.dashboard title="Nilai" description="Lihat nilai materi dan hafalan santri Anda.">
    <section class="space-y-6">
        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-end 2xl:justify-between">
            
            <form method="GET" action="{{ route('guardians.lesson-scores') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto">
               <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama santri" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                <select name="student_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Anak</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}"
                            @selected((string) request('student_id') === (string) $student->id)>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
                
                {{-- <select name="period_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Periode</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) request('period_id', $selectedPeriodId) === (string) $period->id)">{{ $period->name }} - {{ $period->semester }}</option>
                    @endforeach
                </select> --}}
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full text-left" style="min-width: 700px;">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th> 
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
                           
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $assessments->links() }}
    </section>
</x-layouts.dashboard>
