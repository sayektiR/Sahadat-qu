<x-layouts.dashboard title="Rapor" description="Lihat data rapor dari semua cabang.">
    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('leader.reports') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_160px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <select name="branch_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
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
                <div class="flex gap-2">
                    <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[1000px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">Cabang</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Wali Santri</th>
                        <th class="px-6 py-5 font-bold">Periode</th>
                        <th class="px-6 py-5 font-bold">Tanggal</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $reports->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $report->student?->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->branch?->name ?: $report->student?->branch?->name ?: $report->student?->group?->branch?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->student?->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->student?->guardian?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->period?->name }} - {{ $report->period?->semester }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->report_date?->format('d M Y') ?: '-' }}</td>
                            <td class="px-6 py-5">
                                <a href="{{ route('leader.reports.show', $report) }}" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat rapor"><x-icon name="eye" /></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data rapor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $reports->links() }}
    </section>
</x-layouts.dashboard>
