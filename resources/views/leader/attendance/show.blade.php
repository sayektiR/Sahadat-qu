@php
    $statusLabels = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'alpha' => 'Alpha',
    ];
@endphp

<x-layouts.dashboard title="Detail Presensi" description="Detail presensi tanggal {{ $attendance->attendance_date->format('d M Y') }}.">
    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.attendance') }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">Presensi {{ $attendance->attendance_date->format('d M Y') }}</h2>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Cabang</p>
                <p class="mt-2 text-xl font-bold text-blue-950">{{ $attendance->branch?->name ?: $attendance->group?->branch?->name ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Kelompok</p>
                <p class="mt-2 text-xl font-bold text-blue-950">{{ $attendance->group?->name ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Guru</p>
                <p class="mt-2 text-xl font-bold text-blue-950">{{ $attendance->teacher?->name ?: '-' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Pertemuan</p>
                <p class="mt-2 text-xl font-bold text-blue-950">Ke-{{ $attendance->meeting_number }}</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            @foreach ($statusLabels as $status => $label)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-bold text-blue-950">{{ $summary[$status] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[700px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendance->details as $detail)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $loop->iteration }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $detail->student?->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $statusLabels[$detail->status] ?? ucfirst($detail->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada detail presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.dashboard>
