<x-layouts.dashboard title="Detail Santri" description="Detail informasi santri {{ $student->name }}.">
    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.students') }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">{{ $student->name }}</h2>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Data Diri</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIS</span>
                        <span class="font-medium text-slate-950">{{ $student->nis ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jenis Kelamin</span>
                        <span class="font-medium text-slate-950">{{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Lahir</span>
                        <span class="font-medium text-slate-950">{{ $student->birth_date?->format('d M Y') ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-950">Data Akademik</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cabang</span>
                        <span class="font-medium text-slate-950">{{ $student->branch?->name ?: $student->group?->branch?->name ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelompok</span>
                        <span class="font-medium text-slate-950">{{ $student->group?->name ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-lg font-bold text-slate-950">Wali Santri</h3>
            @if ($student->guardian)
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama</span>
                        <span class="font-medium text-slate-950">{{ $student->guardian->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Hubungan</span>
                        <span class="font-medium text-slate-950">{{ $student->guardian->relation ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Telepon</span>
                        <span class="font-medium text-slate-950">{{ $student->guardian->phone ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alamat</span>
                        <span class="font-medium text-slate-950">{{ $student->guardian->address ?: '-' }}</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500">Tidak ada data wali santri.</p>
            @endif
        </div>
    </section>
</x-layouts.dashboard>
