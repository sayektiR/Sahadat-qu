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
            <p class="text-sm text-slate-500">Tidak ada data kelompok</p>
        </div>
    </section>
</x-layouts.dashboard>
