<x-layouts.dashboard title="Detail Cabang" description="Detail informasi cabang {{ $branch->name }}.">
    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.branches') }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">{{ $branch->name }}</h2>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Admin Cabang</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $branch->users->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Santri</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $branch->students->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Guru</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $branch->teachers->count() }}</p>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Informasi Cabang</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alamat</span>
                        <span class="font-medium text-slate-950">{{ $branch->address ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Telepon</span>
                        <span class="font-medium text-slate-950">{{ $branch->phone ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Periode Aktif</h3>
                @if ($branch->periods->where('is_active', true)->isNotEmpty())
                    @php $activePeriod = $branch->periods->where('is_active', true)->first(); @endphp
                    <p class="text-sm text-slate-600">{{ $activePeriod->name }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $activePeriod->academic_year }} - {{ $activePeriod->semester }}</p>
                @else
                    <p class="text-sm text-slate-500">Belum ada periode aktif.</p>
                @endif
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-slate-950">Kelompok</h3>
            @if ($branch->groups->isNotEmpty())
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full min-w-[400px] text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-4 py-3 font-bold">Nama</th>
                                <th class="px-4 py-3 font-bold">Jumlah Santri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branch->groups as $group)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm font-medium">{{ $group->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $group->students->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500">Belum ada kelompok.</p>
            @endif
        </div>
    </section>
</x-layouts.dashboard>