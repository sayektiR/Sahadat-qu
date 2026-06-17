<x-layouts.dashboard title="Rapor" description="Lihat dan unduh rapor santri Anda.">
    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('guardians.reports') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[170px_auto]">
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
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
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
                            <td class="px-6 py-5 text-sm">{{ $report->student?->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->period?->name }} - {{ $report->period?->semester }}</td>
                            <td class="px-6 py-5 text-sm">{{ $report->report_date?->format('d M Y') }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('guardians.reports.show', $report) }}" target="_blank" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat rapor"><x-icon name="eye" /></a>
                                    <a href="{{ route('guardians.reports.show', $report) }}?print=1" target="_blank" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Unduh PDF"><x-icon name="download" /></a>
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
