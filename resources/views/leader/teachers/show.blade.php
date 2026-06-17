<x-layouts.dashboard title="Detail Guru" description="Detail informasi guru {{ $teacher->name }}.">
    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.teachers') }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">{{ $teacher->name }}</h2>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Data Diri</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email</span>
                        <span class="font-medium text-slate-950">{{ $teacher->user?->email ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Telepon</span>
                        <span class="font-medium text-slate-950">{{ $teacher->phone ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Data Akademik</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cabang</span>
                        <span class="font-medium text-slate-950">{{ $teacher->branch?->name ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-slate-950">Kelompok</h3>
            @if ($teacher->groups->isNotEmpty())
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full min-w-[400px] text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-4 py-3 font-bold">Nama</th>
                                <th class="px-4 py-3 font-bold">Cabang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teacher->groups as $group)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm font-medium">{{ $group->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $group->branch?->name ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500">Tidak ada kelompok.</p>
            @endif
        </div>
    </section>
</x-layouts.dashboard>
