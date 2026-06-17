<x-layouts.dashboard title="Jadwal" description="Kelola jadwal belajar setiap kelompok dan periode aktif.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.schedules') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari jadwal" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <select name="group_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Kelompok</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
                    <a href="{{ route('admin.schedules.create') }}" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Jadwal
                    </a>
                </div>
            </form>
        </div>

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
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-slate-900 hover:text-blue-950" aria-label="Edit jadwal"><x-icon name="pencil" /></a>
                            <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cursor-pointer text-slate-900 hover:text-red-600" aria-label="Hapus jadwal"><x-icon name="trash" /></button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <div class="flex min-w-[760px] overflow-hidden rounded-md border border-slate-200">
                            @foreach ($days as $day)
                                <div class="min-w-0 flex-1 border-r border-slate-200 last:border-r-0">
                                    <div class="bg-slate-50 px-3 py-3 text-center text-sm font-bold text-slate-900">{{ $day }}</div>
                                    <div class="min-h-32 border-t border-slate-200">
                                        @forelse (($details[$day] ?? collect())->sortBy('order_number') as $detail)
                                            <div class="border-b border-slate-100 px-3 py-3 text-center text-sm text-slate-700 last:border-b-0">
                                                {{ $detail->subject?->name ?: $detail->material_name }}
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
                    Jadwal belum tersedia.
                </div>
            @endforelse
        </div>

        {{ $schedules->links() }}
    </section>
</x-layouts.dashboard>
