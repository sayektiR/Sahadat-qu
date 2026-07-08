<x-layouts.dashboard title="Jadwal" description="Lihat jadwal belajar pada kelompok yang diampu.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Guru</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $teacher->name }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Kelompok Diampu</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $groups->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Periode Aktif</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $activePeriod?->semester ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Jadwal</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $schedules->total() }}</p>
            </div>
        </div>

       <div class="flex justify-end">
            <form method="GET" id="filterForm" action="{{ route('teachers.schedules') }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">

                <div class="relative w-full sm:w-[220px]">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="search" value="{{ request('search') }}" oninput="submitFilter()" placeholder="Cari jadwal" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <select name="period_id" onchange="submitFilter()" class="h-9 w-full rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10 sm:w-[180px]">
                    <option value="">Pilih Periode</option>

                    @foreach ($periods as $period)
                        <option
                            value="{{ $period->id }}"
                            @selected((string) request('period_id', $activePeriod?->id) === (string) $period->id)>
                            {{ $period->name }} - {{ $period->semester }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($nextSchedule)
            <article class="rounded-lg border border-blue-950/20 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Jadwal Terdekat</p>
                        <h3 class="mt-1 text-xl font-bold text-slate-950">{{ $nextSchedule->group?->name }} - {{ $nextSchedule->period?->name }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $nextSchedule->start_date->format('d M Y') }} - {{ $nextSchedule->end_date->format('d M Y') }} | {{ substr($nextSchedule->start_time, 0, 5) }} - {{ substr($nextSchedule->end_time, 0, 5) }}</p>
                    </div>
                    <div class="rounded-md bg-slate-50 px-4 py-3 text-sm font-semibold text-blue-950">{{ $nextSchedule->total_meetings }} pertemuan</div>
                </div>
            </article>
        @endif

        <div class="grid gap-5">
            @forelse ($schedules as $schedule)
                @php
                    $details = $schedule->details->groupBy('day');
                @endphp
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-950">{{ $schedule->group?->name }} - {{ $schedule->period?->name }}</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $schedule->start_date->format('d M Y') }} - {{ $schedule->end_date->format('d M Y') }}
                                <span class="mx-2">|</span>
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                <span class="mx-2">|</span>
                                {{ $schedule->total_meetings }} pertemuan
                            </p>
                        </div>
                        <a href="{{ route('teachers.attendance', ['group_id' => $schedule->group_id]) }}" class="inline-flex h-10 items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">
                            Isi Presensi
                        </a>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <div class="flex min-w-[760px] overflow-hidden rounded-md border border-slate-200">
                            @foreach ($days as $day)
                                <div class="min-w-0 flex-1 border-r border-slate-200 last:border-r-0">
                                    <div class="bg-slate-50 px-3 py-3 text-center text-sm font-bold text-slate-900">{{ $day }}</div>
                                    <div class="min-h-32 border-t border-slate-200">
                                        @forelse (($details[$day] ?? collect())->sortBy('order_number') as $detail)
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
                    Belum ada jadwal untuk kelompok yang diampu.
                </div>
            @endforelse
        </div>

        {{ $schedules->links() }}
    </section>

    <script>
        let timer;

        function submitFilter() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); 
        }
    </script>
</x-layouts.dashboard>
