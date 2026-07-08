<x-layouts.dashboard title="Jadwal" description="Lihat jadwal belajar santri Anda.">
    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('guardians.schedules') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[170px_auto]">
                <select name="period_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Periode</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) request('period_id', $selectedPeriodId) === (string) $period->id)>{{ $period->name }} - {{ $period->semester }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
            </form>
        </div>

        @forelse ($schedules as $schedule)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-950">{{ $schedule->all_groups ? 'Semua Kelompok' : $schedule->group?->name }} - {{ $schedule->period?->name }}</h3>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ $schedule->start_date->format('d M Y') }} - {{ $schedule->end_date->format('d M Y') }}
                            <span class="mx-2">|</span>
                            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                            <span class="mx-2">|</span>
                            {{ $schedule->total_meetings }} pertemuan
                        </p>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <div class="flex min-w-[760px] overflow-hidden rounded-md border border-slate-200">
                        @foreach ($days as $day)
                            <div class="min-w-0 flex-1 border-r border-slate-200 last:border-r-0">
                                <div class="bg-slate-50 px-3 py-3 text-center text-sm font-bold text-slate-900">{{ $day }}</div>
                                <div class="min-h-32 border-t border-slate-200">
                                    @forelse (($schedule->details->where('day', $day) ?? collect())->sortBy('order_number') as $detail)
                                        <div class="border-b border-slate-100 px-3 py-3 text-center text-sm text-slate-700 last:border-b-0">
                                            <span class="font-semibold">{{ $detail->subject?->name ?: $detail->material_name }}</span>
                                            @if ($detail->subject?->description)
                                                <p class="mt-1 text-xs text-slate-500">{{ $detail->subject->description }}</p>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="px-3 py-8 text-center text-sm text-slate-400">-</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
                Belum ada jadwal.
            </div>
        @endforelse
    </section>
</x-layouts.dashboard>
